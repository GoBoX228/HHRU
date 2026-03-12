const initChatPage = () => {
    const chatContainer = document.getElementById('chat-messages');
    const input = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');

    if (!chatContainer) {
        return;
    }

    const messagesUrl = chatContainer.dataset.messagesUrl;
    const currentUserId = Number(chatContainer.dataset.currentUserId || 0);

    const getLastMessageId = () => {
        const messageNodes = chatContainer.querySelectorAll('[data-message-id]');
        const lastNode = messageNodes[messageNodes.length - 1];

        return lastNode ? Number(lastNode.getAttribute('data-message-id')) : 0;
    };

    let lastMessageId = getLastMessageId();
    let isLoading = false;

    const scrollToBottom = (behavior = 'auto') => {
        const messagesEnd = document.getElementById('messages-end');
        if (messagesEnd) {
            messagesEnd.scrollIntoView({ behavior, block: 'end' });
        }
    };

    const createMessageNode = (message, isMine) => {
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${isMine ? 'justify-end' : ''}`.trim();
        wrapper.dataset.messageId = String(message.id);

        const bubble = document.createElement('div');
        bubble.className = `message ${isMine ? 'me' : 'other'}`;

        const text = document.createElement('p');
        text.className = 'message-text';
        text.textContent = message.text;

        const time = document.createElement('p');
        time.className = 'message-time';
        time.textContent = message.time || '';

        bubble.appendChild(text);
        bubble.appendChild(time);
        wrapper.appendChild(bubble);

        return wrapper;
    };

    const appendMessages = (messages) => {
        if (!Array.isArray(messages) || messages.length === 0) {
            return;
        }

        const shouldStickToBottom = chatContainer.scrollTop + chatContainer.clientHeight >= chatContainer.scrollHeight - 40;
        const emptyState = chatContainer.querySelector('[data-chat-empty]');
        if (emptyState) {
            emptyState.remove();
        }

        const anchor = document.getElementById('messages-end');

        messages.forEach((message) => {
            const isMine = Number(message.sender_user_id) === currentUserId;
            const node = createMessageNode(message, isMine);

            if (anchor) {
                chatContainer.insertBefore(node, anchor);
            } else {
                chatContainer.appendChild(node);
            }

            lastMessageId = Math.max(lastMessageId, Number(message.id));
        });

        if (shouldStickToBottom) {
            scrollToBottom('smooth');
        }
    };

    const fetchNewMessages = async () => {
        if (!messagesUrl || isLoading) {
            return;
        }

        isLoading = true;

        try {
            const url = new URL(messagesUrl, window.location.origin);
            url.searchParams.set('after', String(lastMessageId));

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            appendMessages(payload.messages || []);
        } catch (_error) {
            // Ignore transient polling errors.
        } finally {
            isLoading = false;
        }
    };

    if (input && sendButton) {
        const updateState = () => {
            const hasText = input.value.trim().length > 0;
            sendButton.disabled = !hasText;
            sendButton.style.opacity = hasText ? '1' : '0.5';
        };

        input.addEventListener('input', updateState);
        updateState();
    }

    scrollToBottom();
    const pollTimer = setInterval(fetchNewMessages, 5000);
    window.addEventListener('beforeunload', () => clearInterval(pollTimer));
};

const initProfilePage = () => {
    const openButton = document.querySelector('[data-profile-edit-open]');
    const cancelButton = document.querySelector('[data-profile-edit-cancel]');
    const editForm = document.querySelector('[data-profile-edit-form]');
    const viewBlock = document.querySelector('[data-profile-view]');
    const aboutInput = document.querySelector('[data-about-input]');
    const aboutCounter = document.querySelector('[data-about-counter]');
    const skillsInput = document.querySelector('[data-skills-input]');
    const skillSelect = document.querySelector('[data-skill-select]');
    const selectedSkillsContainer = document.querySelector('[data-skills-selected]');

    if (aboutInput && aboutCounter) {
        aboutInput.addEventListener('input', () => {
            aboutCounter.textContent = String(aboutInput.value.length);
        });
    }

    if (!skillsInput || !skillSelect || !selectedSkillsContainer) {
        return;
    }

    const emptyHintText = selectedSkillsContainer.querySelector('[data-skills-empty]')?.textContent?.trim() || 'Навыки не выбраны';

    const parseSkills = (value) => value
        .split(',')
        .map((skill) => skill.trim())
        .filter(Boolean)
        .filter((skill, index, array) => array.indexOf(skill) === index)
        .slice(0, 20);

    let selectedSkills = parseSkills(skillsInput.value || '');

    const syncSkillsInput = () => {
        skillsInput.value = selectedSkills.join(', ');
    };

    const renderSelectedSkills = () => {
        selectedSkillsContainer.innerHTML = '';

        if (selectedSkills.length === 0) {
            const emptyHint = document.createElement('span');
            emptyHint.className = 'text-sm text-muted';
            emptyHint.dataset.skillsEmpty = 'true';
            emptyHint.textContent = emptyHintText;
            selectedSkillsContainer.appendChild(emptyHint);
            return;
        }

        selectedSkills.forEach((skill) => {
            const chip = document.createElement('span');
            chip.className = 'badge badge-primary skill-chip';

            const label = document.createElement('span');
            label.textContent = skill;
            chip.appendChild(label);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'skill-chip-remove';
            removeButton.dataset.skillRemove = skill;
            removeButton.setAttribute('aria-label', `Удалить навык ${skill}`);
            removeButton.innerHTML = '&times;';
            chip.appendChild(removeButton);

            selectedSkillsContainer.appendChild(chip);
        });
    };

    const addSelectedSkill = () => {
        const skill = skillSelect.value.trim();
        if (!skill || selectedSkills.includes(skill) || selectedSkills.length >= 20) {
            return;
        }

        selectedSkills.push(skill);
        syncSkillsInput();
        renderSelectedSkills();
        skillSelect.value = '';
    };

    skillSelect.addEventListener('change', addSelectedSkill);

    selectedSkillsContainer.addEventListener('click', (event) => {
        const target = event.target.closest('[data-skill-remove]');
        if (!target) {
            return;
        }

        const skill = target.dataset.skillRemove;
        selectedSkills = selectedSkills.filter((item) => item !== skill);
        syncSkillsInput();
        renderSelectedSkills();
    });

    syncSkillsInput();
    renderSelectedSkills();

    if (!openButton || !cancelButton || !editForm || !viewBlock) {
        return;
    }

    const shouldOpenEdit = editForm.dataset.openOnLoad === '1';

    const setEditingState = (isEditing) => {
        editForm.classList.toggle('hidden', !isEditing);
        viewBlock.classList.toggle('hidden', isEditing);
        openButton.classList.toggle('hidden', isEditing);
    };

    openButton.addEventListener('click', () => setEditingState(true));
    cancelButton.addEventListener('click', () => setEditingState(false));

    setEditingState(shouldOpenEdit);
};

const boot = () => {
    initChatPage();
    initProfilePage();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}