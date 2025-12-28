// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Full page chat functionality for Teacher Assistant.
 *
 * @module     local_teacherassistant/chat
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

class ChatManager {
    constructor(courseId) {
        this.courseId = courseId;
    }

    /**
     * Initialize the full page chat.
     */
    async init() {
        await this.render();
        this.attachEventHandlers();
    }

    /**
     * Render the chat interface.
     */
    async render() {
        console.log('Rendering chat interface...');
        try {
            const context = {
                courseid: this.courseId
            };

            const html = await Templates.render('local_teacherassistant/chat_fullpage', context);
            const container = document.getElementById('teacherassistant-chat-container');
            if (container) {
                container.innerHTML = html;
            }
        } catch (error) {
            Notification.exception(error);
        }
    }

    /**
     * Attach event handlers.
     */
    attachEventHandlers() {
        const sendBtn = document.getElementById('teacherassistant-fullpage-send-btn');
        const input = document.getElementById('teacherassistant-fullpage-input');

        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }

        if (input) {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.ctrlKey && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }
    }

    /**
     * Send a message.
     */
    async sendMessage() {
        const input = document.getElementById('teacherassistant-fullpage-input');
        const message = input?.value.trim();

        if (!message) {
            return;
        }

        // Add user message to chat
        this.addMessage(message, 'user');

        // Clear input
        input.value = '';

        // Show loading indicator
        this.showLoading();

        try {
            // Make AJAX call to backend
            const response = await Ajax.call([{
                methodname: 'local_teacherassistant_send_message',
                args: {
                    courseid: this.courseId,
                    message: message
                }
            }])[0];

            this.hideLoading();
            this.addMessage(response.message, 'assistant');
        } catch (error) {
            this.hideLoading();
            Notification.exception(error);
        }
    }

    /**
     * Add a message to the chat.
     *
     * @param {string} text - The message text
     * @param {string} sender - 'user' or 'assistant'
     */
    addMessage(text, sender) {
        const messagesContainer = document.getElementById('teacherassistant-fullpage-messages');
        if (!messagesContainer) {
            return;
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `teacherassistant-fullpage-message teacherassistant-fullpage-message-${sender}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'teacherassistant-fullpage-message-content';
        contentDiv.textContent = text;

        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    /**
     * Show loading indicator.
     */
    showLoading() {
        const messagesContainer = document.getElementById('teacherassistant-fullpage-messages');
        if (!messagesContainer) {
            return;
        }

        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'teacherassistant-fullpage-loading';
        loadingDiv.className = 'teacherassistant-fullpage-message teacherassistant-fullpage-message-assistant';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'teacherassistant-fullpage-message-content';
        contentDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Thinking...';

        loadingDiv.appendChild(contentDiv);
        messagesContainer.appendChild(loadingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    /**
     * Hide loading indicator.
     */
    hideLoading() {
        const loading = document.getElementById('teacherassistant-fullpage-loading');
        if (loading) {
            loading.remove();
        }
    }
}

export const init = (courseId) => {
    console.log('Initializing Teacher Assistant full page chat...');
    console.log(`Course ID: ${courseId}`);
    const manager = new ChatManager(courseId);
    console.log('ChatManager instance created.');
    console.log(manager);
    manager.init();
};
