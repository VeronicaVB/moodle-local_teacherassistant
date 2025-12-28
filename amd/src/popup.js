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
 * Teacher Assistant popup functionality.
 *
 * @module     local_teacherassistant/popup
 * @copyright  2025 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

class PopupManager {
    constructor(courseId) {
        this.isMinimized = true;
        this.courseId = courseId;
    }

    /**
     * Initialize the popup.
     */
    async init() {
        await this.render();
        this.attachEventHandlers();
    }

    /**
     * Render the popup template.
     */
    async render() {
        console.log('Rendering Teacher Assistant popup');
        try {
          
             let container = document.getElementById('teacherassistant-container');

            if (!container) {
                container = document.createElement('div');
                container.id = 'teacherassistant-container';
                document.body.appendChild(container);
            }


            const context = {
                courseid: this.courseId
            };

            console.log('Rendering popup with context:', context);
            const html = await Templates.render('local_teacherassistant/popup', context);
            // const container = document.getElementById('teacherassistant-container');

            console.log(html);
            if (container) {
                container.innerHTML = html;
            }
        } catch (error) {
            Notification.exception(error);
        }
    }

    /**
     * Attach event handlers to popup elements.
     */
    attachEventHandlers() {
        const toggleBtn = document.getElementById('teacherassistant-toggle-btn');
        const minimizeBtn = document.getElementById('teacherassistant-minimize-btn');
        const expandBtn = document.getElementById('teacherassistant-expand-btn');
        const sendBtn = document.getElementById('teacherassistant-send-btn');
        const input = document.getElementById('teacherassistant-input');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.togglePopup());
        }

        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', () => this.minimizePopup());
        }

        if (expandBtn) {
            expandBtn.addEventListener('click', () => this.expandToFullPage());
        }

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
     * Toggle popup visibility.
     */
    togglePopup() {
        const popup = document.getElementById('teacherassistant-popup');
        const content = document.getElementById('teacherassistant-content');

        if (!popup || !content) {
            return;
        }

        if (this.isMinimized) {
            popup.classList.remove('teacherassistant-minimized');
            popup.classList.add('teacherassistant-expanded');
            content.style.display = 'flex';
            this.isMinimized = false;
        } else {
            this.minimizePopup();
        }
    }

    /**
     * Minimize the popup.
     */
    minimizePopup() {
        const popup = document.getElementById('teacherassistant-popup');
        const content = document.getElementById('teacherassistant-content');

        if (!popup || !content) {
            return;
        }

        content.style.display = 'none';
        popup.classList.remove('teacherassistant-expanded');
        popup.classList.add('teacherassistant-minimized');
        this.isMinimized = true;
    }

    /**
     * Expand to full page.
     */
    expandToFullPage() {
        if (this.courseId) {
            const url = `${M.cfg.wwwroot}/local/teacherassistant/chat.php?courseid=${this.courseId}`;
            window.open(url, '_blank');
        }
    }

    /**
     * Send a message.
     */
    async sendMessage() {
        const input = document.getElementById('teacherassistant-input');
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
        const messagesContainer = document.getElementById('teacherassistant-messages');
        if (!messagesContainer) {
            return;
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `teacherassistant-message teacherassistant-message-${sender}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'teacherassistant-message-content';
        contentDiv.textContent = text;

        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    /**
     * Show loading indicator.
     */
    showLoading() {
        const messagesContainer = document.getElementById('teacherassistant-messages');
        if (!messagesContainer) {
            return;
        }

        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'teacherassistant-loading';
        loadingDiv.className = 'teacherassistant-message teacherassistant-message-assistant';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'teacherassistant-message-content';
        contentDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Thinking...';

        loadingDiv.appendChild(contentDiv);
        messagesContainer.appendChild(loadingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    /**
     * Hide loading indicator.
     */
    hideLoading() {
        const loading = document.getElementById('teacherassistant-loading');
        if (loading) {
            loading.remove();
        }
    }
}

export const init = (courseId) => {
    console.log('Teacher Assistant popup initialized');
    const manager = new PopupManager(courseId);
    manager.init();
};
