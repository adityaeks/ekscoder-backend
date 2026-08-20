<x-admin-layout title="AI Chat (9Router)" breadcrumb="AI Tools / AI Chat">

    <!-- Dependencies: Marked.js & Highlight.js for Markdown & Code Highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <style>
        .page-content {
            padding: 16px 24px !important;
        }

        .ai-layout-wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: calc(100vh - 92px);
            max-height: calc(100vh - 92px);
            overflow: hidden;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            color: var(--text-primary, #f0f0f5);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .status-pill:hover {
            border-color: var(--green, #22c55e);
            background: var(--bg-hover);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green, #22c55e);
            box-shadow: 0 0 8px var(--green, #22c55e);
            transition: all 0.3s ease;
        }

        .status-dot.offline {
            background: var(--rose, #f43f5e);
            box-shadow: 0 0 8px var(--rose, #f43f5e);
        }

        .status-dot.checking {
            background: var(--amber, #f59e0b);
            box-shadow: 0 0 8px var(--amber, #f59e0b);
            animation: pulse 1s infinite alternate;
        }

        @keyframes pulse {
            from { opacity: 0.4; }
            to { opacity: 1; }
        }

        .btn-top-action {
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            color: var(--text-primary, #f0f0f5);
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 500;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-top-action:hover {
            background: var(--bg-hover, #1c1c28);
            border-color: var(--border-light);
            transform: translateY(-1px);
        }

        /* Main Grid & Flex Layout */
        .ai-chat-grid {
            display: flex;
            gap: 16px;
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        @media (max-width: 900px) {
            .ai-sidebar {
                width: 0 !important;
                opacity: 0 !important;
                pointer-events: none !important;
                border-color: transparent !important;
            }
        }

        /* Sidebar Styles */
        .ai-sidebar {
            width: 280px;
            flex-shrink: 0;
            background: var(--bg-surface, #111118);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, border-color 0.25s ease;
            opacity: 1;
        }

        .ai-chat-grid.sidebar-collapsed .ai-sidebar {
            width: 8px !important;
            min-width: 8px;
            opacity: 0;
            pointer-events: none;
            border-color: transparent;
            background: transparent;
        }

        .ai-sidebar-top {
            padding: 14px;
            border-bottom: 1px solid var(--border, rgba(255, 255, 255, 0.07));
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-create-chat {
            width: 100%;
            background: linear-gradient(135deg, var(--green, #22c55e) 0%, #16a34a 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.25);
        }

        .btn-create-chat:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.35);
        }

        .sidebar-search-input {
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 8px;
            color: var(--text-primary, #f0f0f5);
            padding: 7px 12px;
            font-size: 12px;
            width: 100%;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .sidebar-search-input:focus {
            border-color: var(--green, #22c55e);
        }

        .sidebar-search-input::placeholder {
            color: var(--text-muted, #55555f);
        }

        .thread-list-container {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .thread-item {
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            color: var(--text-secondary, #8b8ba0);
            font-size: 13px;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            gap: 8px;
            position: relative;
        }

        .thread-item:hover {
            background: var(--bg-hover, #1c1c28);
            color: var(--text-primary, #f0f0f5);
        }

        .thread-item.pinned {
            background: rgba(245, 158, 11, 0.05);
            border-color: rgba(245, 158, 11, 0.15);
        }

        .thread-item.pinned:hover {
            background: var(--bg-hover, #1c1c28);
            border-color: rgba(245, 158, 11, 0.35);
        }

        .thread-item.active {
            background: var(--green-soft, rgba(34, 197, 94, 0.12));
            color: var(--green, #22c55e);
            border-color: rgba(34, 197, 94, 0.3);
            font-weight: 600;
        }

        .thread-item.pinned.active {
            background: var(--green-soft, rgba(34, 197, 94, 0.12));
            border-color: rgba(34, 197, 94, 0.35);
        }

        .thread-pin-badge {
            color: var(--amber, #f59e0b);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            opacity: 0.85;
            transition: opacity 0.15s ease;
        }

        .thread-item:hover .thread-pin-badge {
            display: none;
        }

        .thread-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .thread-actions {
            display: none;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .thread-item:hover .thread-actions {
            display: flex;
        }

        .btn-thread-action {
            background: none;
            border: none;
            color: var(--text-muted, #55555f);
            padding: 3px 5px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .btn-thread-action.btn-pin {
            color: var(--text-muted, #55555f);
        }

        .btn-thread-action.btn-pin.is-pinned {
            color: var(--amber, #f59e0b);
        }

        .btn-thread-action.btn-pin:hover {
            color: var(--amber, #f59e0b);
            background: rgba(245, 158, 11, 0.15);
        }

        .btn-thread-action.btn-delete:hover {
            color: var(--rose, #f43f5e);
            background: var(--rose-soft, rgba(244, 63, 94, 0.15));
        }

        #btnPinCurrentChat {
            transition: all 0.2s ease;
        }

        #btnPinCurrentChat.is-pinned {
            color: var(--amber, #f59e0b);
            border-color: rgba(245, 158, 11, 0.4);
            background: rgba(245, 158, 11, 0.12);
        }

        /* Main Chat Window */
        .ai-chat-window {
            flex: 1;
            min-width: 0;
            background: var(--bg-surface, #111118);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chat-header-bar {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border, rgba(255, 255, 255, 0.07));
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-surface, #111118);
        }

        .active-thread-title-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
            margin-right: 12px;
        }

        .active-thread-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary, #f0f0f5);
            background: transparent !important;
            outline: none;
            border: 1px solid transparent;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
            width: 100%;
            min-width: 0;
            text-overflow: ellipsis;
        }

        .active-thread-title:hover {
            border-color: var(--border-light);
            background: var(--bg-hover) !important;
        }

        .active-thread-title:focus {
            border-color: var(--green, #22c55e);
            background: var(--bg-elevated) !important;
            box-shadow: 0 0 0 2px var(--green-soft);
        }

        .select-model-dropdown {
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            color: var(--text-primary, #f0f0f5);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            outline: none;
            cursor: pointer;
        }

        .select-model-dropdown option {
            background: var(--bg-surface, #111118);
            color: var(--text-primary, #f0f0f5);
        }

        .chat-body-feed {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            scroll-behavior: smooth;
        }

        /* Message Bubbles */
        .chat-msg-row {
            display: flex;
            gap: 12px;
            max-width: 88%;
        }

        .chat-msg-row.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .chat-msg-row.assistant {
            align-self: flex-start;
        }

        .chat-msg-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .chat-msg-row.user .chat-msg-avatar {
            background: linear-gradient(135deg, var(--accent, #6c63ff) 0%, #4f46e5 100%);
            color: #ffffff;
        }

        .chat-msg-row.assistant .chat-msg-avatar {
            background: linear-gradient(135deg, var(--green, #22c55e) 0%, #16a34a 100%);
            color: #ffffff;
        }

        .chat-msg-bubble {
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            padding: 12px 16px;
            border-radius: 14px;
            color: var(--text-primary, #f0f0f5);
            font-size: 13.5px;
            line-height: 1.6;
            word-break: break-word;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .chat-msg-row.user .chat-msg-bubble {
            background: var(--accent-soft, rgba(108, 99, 255, 0.15));
            border-color: rgba(108, 99, 255, 0.35);
            color: var(--text-primary, #f0f0f5);
            border-bottom-right-radius: 2px;
        }

        .chat-msg-row.assistant .chat-msg-bubble {
            background: var(--bg-elevated, #16161f);
            border-bottom-left-radius: 2px;
        }

        /* Formatted Markdown inside Bubbles */
        .chat-msg-bubble p {
            margin: 0 0 10px 0;
        }
        .chat-msg-bubble p:last-child {
            margin-bottom: 0;
        }

        .chat-msg-bubble ul, .chat-msg-bubble ol {
            margin: 6px 0 10px 20px;
            padding: 0;
        }

        .chat-msg-bubble table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 13px;
            background: var(--bg-surface);
            border-radius: 8px;
            overflow: hidden;
        }

        .chat-msg-bubble th, .chat-msg-bubble td {
            padding: 8px 12px;
            border: 1px solid var(--border-light);
            text-align: left;
        }

        .chat-msg-bubble th {
            background: var(--bg-hover);
            color: var(--green, #22c55e);
            font-weight: 600;
        }

        .chat-msg-bubble code:not(pre code) {
            background: var(--green-soft, rgba(34, 197, 94, 0.12));
            border: 1px solid rgba(34, 197, 94, 0.25);
            padding: 2px 6px;
            border-radius: 5px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--green, #22c55e);
        }

        /* Custom Code Blocks with Header & Copy Button */
        .code-block-card {
            background: #0d1117;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            margin: 12px 0;
            overflow: hidden;
        }

        .code-block-header {
            background: #161b22;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 11px;
            font-family: 'JetBrains Mono', monospace;
            color: #8b949e;
            text-transform: uppercase;
        }

        .btn-copy-code {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #8b949e;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy-code:hover {
            background: var(--green, #22c55e);
            color: #ffffff;
            border-color: var(--green, #22c55e);
        }

        .code-block-card pre {
            margin: 0;
            padding: 12px 14px;
            overflow-x: auto;
        }

        .code-block-card pre code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            line-height: 1.5;
            color: #e6edf3;
        }

        /* Bubble Footer Actions */
        .bubble-footer-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid var(--border, rgba(255, 255, 255, 0.05));
            font-size: 11px;
        }

        .btn-bubble-action {
            background: none;
            border: none;
            color: var(--text-muted, #55555f);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 6px;
            border-radius: 4px;
            transition: all 0.15s ease;
        }

        .btn-bubble-action:hover {
            color: var(--text-primary, #f0f0f5);
            background: var(--bg-hover);
        }

        /* Empty State Screen */
        .chat-empty-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 30px 20px;
            color: var(--text-secondary, #8b8ba0);
        }

        .empty-hero-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: var(--green-soft, rgba(34, 197, 94, 0.12));
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: var(--green, #22c55e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 14px;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.15);
        }

        .prompt-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            width: 100%;
            max-width: 620px;
            margin-top: 20px;
        }

        .prompt-card {
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 12px;
            padding: 12px 14px;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .prompt-card:hover {
            border-color: var(--green, #22c55e);
            background: var(--bg-hover, #1c1c28);
            transform: translateY(-2px);
        }

        .prompt-card-title {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-primary, #f0f0f5);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .prompt-card-desc {
            font-size: 11.5px;
            color: var(--text-muted, #55555f);
            line-height: 1.4;
        }

        /* Input Area */
        .chat-footer-input-area {
            padding: 14px 18px;
            border-top: 1px solid var(--border, rgba(255, 255, 255, 0.07));
            background: var(--bg-surface, #111118);
        }

        .chat-input-box {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            background: var(--bg-elevated, #16161f);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 12px;
            padding: 8px 12px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .chat-input-box:focus-within {
            border-color: var(--green, #22c55e);
            box-shadow: 0 0 0 3px var(--green-soft);
        }

        .prompt-textarea {
            flex: 1;
            background: transparent !important;
            border: none;
            color: var(--text-primary, #f0f0f5);
            font-size: 13.5px;
            line-height: 1.5;
            resize: none;
            max-height: 140px;
            min-height: 24px;
            outline: none;
            font-family: inherit;
        }

        .prompt-textarea::placeholder {
            color: var(--text-muted, #55555f);
        }

        .btn-send-message {
            background: linear-gradient(135deg, var(--green, #22c55e) 0%, #16a34a 100%);
            color: #ffffff;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(34, 197, 94, 0.3);
        }

        .btn-send-message:hover:not(:disabled) {
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.4);
        }

        .btn-attach-image {
            background: transparent;
            border: none;
            color: var(--text-secondary, #9999a8);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .btn-attach-image:hover {
            color: var(--green, #22c55e);
            background: var(--bg-hover, #1c1c28);
        }

        .image-attachment-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--bg-surface, #111118);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-bottom: none;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            overflow-x: auto;
        }

        .image-attachment-item {
            position: relative;
            width: 58px;
            height: 58px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--green, #22c55e);
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .image-attachment-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-remove-attachment {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(0, 0, 0, 0.75);
            color: #ffffff;
            border: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-remove-attachment:hover {
            background: var(--rose, #f43f5e);
        }

        /* Message Bubble Images */
        .chat-msg-images-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 8px;
        }

        .chat-msg-image-thumb {
            max-width: 260px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.15));
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            object-fit: contain;
            background: rgba(0, 0, 0, 0.25);
        }

        .chat-msg-image-thumb:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            border-color: var(--green, #22c55e);
        }

        /* Typing Dots Animation */
        .dots-typing {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 6px;
        }

        .dot-bounce {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green, #22c55e);
            animation: dotBounce 1.4s infinite ease-in-out both;
        }

        .dot-bounce:nth-child(1) { animation-delay: -0.32s; }
        .dot-bounce:nth-child(2) { animation-delay: -0.16s; }

        @keyframes dotBounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* Modal Glass Backdrops */
        .modal-glass-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(5px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-glass-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-dialog-box {
            background: var(--bg-surface, #111118);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 18px;
            width: 100%;
            max-width: 520px;
            padding: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            color: var(--text-primary);
        }

        .modal-dialog-box input[type="text"],
        .modal-dialog-box input[type="password"],
        .modal-dialog-box select,
        .modal-dialog-box textarea {
            width: 100%;
            background: var(--bg-elevated) !important;
            border: 1px solid var(--border-light) !important;
            border-radius: 8px;
            color: var(--text-primary) !important;
            padding: 9px 12px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .modal-dialog-box select option {
            background: var(--bg-surface, #111118);
            color: var(--text-primary, #f0f0f5);
        }

        .modal-dialog-box input:focus,
        .modal-dialog-box select:focus,
        .modal-dialog-box textarea:focus {
            border-color: var(--green) !important;
            box-shadow: 0 0 0 2px var(--green-soft);
        }

        /* Toast Notifications */
        .ai-toast-item {
            pointer-events: auto;
            min-width: 280px;
            max-width: 380px;
            background: var(--bg-surface, #111118);
            border: 1px solid var(--border-light, rgba(255, 255, 255, 0.12));
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            color: var(--text-primary, #f0f0f5);
            font-size: 13px;
            animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .ai-toast-item.toast-success {
            border-color: rgba(34, 197, 94, 0.4);
            background: var(--bg-surface);
        }

        .ai-toast-item.toast-error {
            border-color: rgba(244, 63, 94, 0.4);
            background: var(--bg-surface);
        }
    </style>

    <div class="ai-layout-wrapper">
        <!-- Main Chat Area (Sidebar + Chat Window) -->
        <div class="ai-chat-grid">
            <!-- Sidebar Thread List -->
            <div class="ai-sidebar">
                <div class="ai-sidebar-top">
                    <button class="btn-create-chat" onclick="createNewChat()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Percakapan Baru
                    </button>
                    <input type="text" class="sidebar-search-input" id="searchThreadInput" onkeyup="filterThreads()" placeholder="Cari judul percakapan...">
                </div>

                <div class="thread-list-container" id="chatThreadList">
                    <!-- Threads rendered via JS -->
                </div>
            </div>

            <!-- Chat Window -->
            <div class="ai-chat-window">
                <!-- Header -->
                <div class="chat-header-bar">
                    <div class="active-thread-title-wrap">
                        <button onclick="toggleAiThreadSidebar()" class="btn-top-action" style="padding:6px 10px;" title="Buka / Tutup Sidebar">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                        </button>
                        <!-- <span style="display:inline-flex; align-items:center; color:var(--green, #22c55e);"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span> -->
                        <input type="text" class="active-thread-title" id="activeChatTitle" value="Percakapan Baru" onblur="saveCurrentThreadTitle(this.value)" onkeydown="if(event.key==='Enter') this.blur()">
                        <button id="btnPinCurrentChat" onclick="togglePinCurrentConversation()" class="btn-top-action" style="padding:6px 10px; display:none;" title="Sematkan / Lepas Sematan Percakapan ini">
                            <!-- Injected by JS -->
                        </button>
                    </div>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <div class="status-pill" id="headerStatusPill" onclick="toggleSettingsModal()" title="Klik untuk membuka Pengaturan API 9Router">
                            <span class="status-dot" id="connStatusDot"></span>
                            <span id="connStatusText">Memeriksa...</span>
                        </div>

                        <select class="select-model-dropdown" id="modelSelect" onchange="changeModel(this.value)">
                            <option value="Spark">Spark</option>
                            <option value="muse2/muse-spark-1.2">muse2/muse-spark-1.2</option>
                            <option value="gpt-4o">gpt-4o</option>
                            <option value="claude-3-5-sonnet">claude-3-5-sonnet</option>
                        </select>

                        <button onclick="clearCurrentChatMessages()" class="btn-top-action" style="padding:6px 10px;" title="Hapus seluruh riwayat pesan percakapan ini">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>

                        <button onclick="toggleSettingsModal()" class="btn-top-action" style="padding:6px 10px;" title="Buka Pengaturan API 9Router">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Message Feed -->
                <div class="chat-body-feed" id="chatMessagesContainer">
                    <div class="chat-empty-wrapper" id="emptyState">
                        <div class="empty-hero-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8.01" y2="16"/><line x1="16" y1="16" x2="16.01" y2="16"/></svg>
                        </div>
                        <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin:0 0 6px 0;">Asisten AI Ekscoder</h3>
                    </div>
                </div>

                <!-- Input Footer -->
                <div class="chat-footer-input-area">
                    <div id="imageAttachmentBar" class="image-attachment-bar" style="display:none;"></div>
                    <div class="chat-input-box" id="chatInputBox">
                        <input type="file" id="imageFileInput" accept="image/*" multiple style="display:none;" onchange="handleImageFileSelect(event)">
                        <button type="button" class="btn-attach-image" onclick="document.getElementById('imageFileInput').click()" title="Unggah / Lampirkan Gambar (Bisa juga langsung Paste Ctrl+V)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </button>
                        <textarea class="prompt-textarea" id="chatInput" rows="1" placeholder="Ketik pesan atau Paste (Ctrl+V) gambar... (Enter untuk kirim)" onkeydown="handleInputKeydown(event)" oninput="autoResizeTextarea(this)"></textarea>
                        <button class="btn-send-message" id="sendBtn" onclick="sendMessage()" title="Kirim Pesan">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div class="modal-glass-backdrop" id="imageLightboxModal" onclick="closeImageLightbox()">
        <div style="position:relative; max-width:90vw; max-height:90vh; display:flex; justify-content:center; align-items:center;" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" style="max-width:90vw; max-height:85vh; border-radius:12px; object-fit:contain; box-shadow:0 20px 50px rgba(0,0,0,0.8); border:1px solid var(--border-light);">
            <button onclick="closeImageLightbox()" style="position:absolute; top:-14px; right:-14px; background:var(--rose, #f43f5e); color:#fff; border:none; width:32px; height:32px; border-radius:50%; font-size:15px; font-weight:bold; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center;">✕</button>
        </div>
    </div>

    <!-- Modal Pengaturan 9Router -->
    <div class="modal-glass-backdrop" id="settingsModal">
        <div class="modal-dialog-box">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid var(--border, rgba(255,255,255,0.07));">
                <h3 style="font-weight:700; color:var(--text-primary); font-size:15px; margin:0; display:flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Pengaturan 9Router Gateway
                </h3>
                <button onclick="toggleSettingsModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:18px;">✕</button>
            </div>

            <form id="settingsForm" onsubmit="saveSettings(event)">
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; color:var(--text-secondary); margin-bottom:6px; font-weight:600;">9Router Base URL</label>
                    <input type="text" id="settingBaseUrl" value="{{ $settings['base_url'] }}" required>
                    <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:4px;">Default lokal: <code>http://localhost:20128/v1</code></span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; color:var(--text-secondary); margin-bottom:6px; font-weight:600;">API Key 9Router Anda</label>
                    <input type="password" id="settingApiKey" value="{{ $settings['api_key'] }}" placeholder="Masukkan API Key 9Router Anda...">
                    <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:4px;">Ambil dari dashboard 9Router Anda di <code>http://localhost:20128/dashboard</code></span>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <label style="font-size:12px; color:var(--text-secondary); font-weight:600; margin:0;">Default Model</label>
                        <button type="button" onclick="load9RouterModels()" style="background:none; border:none; color:var(--green, #22c55e); font-size:11px; cursor:pointer; display:flex; align-items:center; gap:4px; padding:0;" title="Refresh daftar model dari 9Router">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                            Refresh Model
                        </button>
                    </div>
                    <select id="settingDefaultModel" required>
                        <option value="{{ $settings['default_model'] }}" selected>{{ $settings['default_model'] }}</option>
                    </select>
                    <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:4px;">Pilih model default dari daftar provider yang aktif di 9Router (digunakan untuk auto-generate artikel blog, SEO, dan chat default).</span>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:12px; color:var(--text-secondary); margin-bottom:6px; font-weight:600;">System Prompt</label>
                    <textarea id="settingSystemPrompt" rows="3">{{ $settings['system_prompt'] }}</textarea>
                </div>

                <div id="modalTestResult" style="display:none; margin-bottom:14px; padding:10px 12px; border-radius:8px; font-size:12px;"></div>

                <div style="display:flex; justify-content:flex-end; gap:8px;">
                    <button type="button" onclick="test9RouterConnectionInModal()" style="background:var(--bg-elevated); border:1px solid var(--border-light); color:var(--text-primary); padding:8px 14px; border-radius:8px; cursor:pointer; font-size:12.5px;">Uji Koneksi</button>
                    <button type="submit" style="background:var(--green, #22c55e); border:none; color:#fff; padding:8px 18px; border-radius:8px; cursor:pointer; font-weight:600; font-size:12.5px;">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div class="modal-glass-backdrop" id="aiConfirmModal">
        <div class="modal-dialog-box" style="max-width:440px; text-align:center;">
            <div id="aiConfirmIcon" style="width:52px; height:52px; border-radius:16px; background:var(--rose-soft, rgba(244, 63, 94, 0.15)); border:1px solid rgba(244, 63, 94, 0.3); color:var(--rose, #f43f5e); display:flex; align-items:center; justify-content:center; margin:0 auto 14px auto; box-shadow:0 0 20px rgba(244, 63, 94, 0.2);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </div>
            <h3 id="aiConfirmTitle" style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 6px 0;">Hapus Percakapan</h3>
            <p id="aiConfirmMessage" style="font-size:13px; color:var(--text-secondary); margin:0 0 20px 0; line-height:1.5;">Apakah Anda yakin ingin menghapus percakapan ini?</p>
            <div style="display:flex; gap:10px; justify-content:center;">
                <button onclick="closeConfirmModal(false)" style="flex:1; background:var(--bg-elevated); border:1px solid var(--border-light); color:var(--text-primary); padding:9px 16px; border-radius:10px; font-weight:500; font-size:13px; cursor:pointer; transition:all 0.2s;">Batal</button>
                <button id="aiConfirmSubmitBtn" onclick="closeConfirmModal(true)" style="flex:1; background:linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); border:none; color:#fff; padding:9px 16px; border-radius:10px; font-weight:600; font-size:13px; cursor:pointer; box-shadow:0 4px 14px rgba(244, 63, 94, 0.3); transition:all 0.2s;">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="aiToastContainer" style="position:fixed; bottom:24px; right:24px; z-index:99999; display:flex; flex-direction:column; gap:10px; pointer-events:none;"></div>

    <!-- JavaScript Core Logic -->
    <script>
        let currentConversationId = null;
        let activeConversations = [];
        let isSending = false;
        let confirmResolver = null;
        let attachedImages = [];

        const SVG_ICONS = {
            chat: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,
            pin: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.89A2 2 0 0 1 15 10.77V6a3 3 0 0 0-6 0v4.77a2 2 0 0 1-1.11 1.79l-1.78.89A2 2 0 0 0 5 15.24Z"/></svg>`,
            pinFilled: `<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22" stroke-width="2"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.89A2 2 0 0 1 15 10.77V6a3 3 0 0 0-6 0v4.77a2 2 0 0 1-1.11 1.79l-1.78.89A2 2 0 0 0 5 15.24Z"/></svg>`,
            trash: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>`,
            user: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`,
            bot: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8.01" y2="16"/><line x1="16" y1="16" x2="16.01" y2="16"/></svg>`,
            copy: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>`,
            success: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
            error: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
            warning: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
            info: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`
        };

        function toggleAiThreadSidebar() {
            const gridEl = document.querySelector('.ai-chat-grid');
            if (!gridEl) return;

            const isCollapsed = gridEl.classList.toggle('sidebar-collapsed');
            localStorage.setItem('ai_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isCollapsed = localStorage.getItem('ai_sidebar_collapsed') === 'true';
            
            if (isCollapsed) {
                const gridEl = document.querySelector('.ai-chat-grid');
                if (gridEl) gridEl.classList.add('sidebar-collapsed');
            }

            // Configure Marked.js
            marked.setOptions({
                breaks: true,
                gfm: true
            });

            loadConversations();
            load9RouterModels();
            test9RouterConnection();

            // Global paste listener for images (Ctrl+V)
            window.addEventListener('paste', function(e) {
                if (e.clipboardData && e.clipboardData.items) {
                    const items = e.clipboardData.items;
                    let hasImage = false;
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf('image') !== -1) {
                            const file = items[i].getAsFile();
                            if (file) {
                                readAndAddImageFile(file);
                                hasImage = true;
                            }
                        }
                    }
                    if (hasImage) {
                        e.preventDefault();
                    }
                }
            });
        });

        /* Custom Confirmation Modal Function */
        function customConfirm({ title = 'Konfirmasi', message = 'Apakah Anda yakin?', icon = '', confirmText = 'Ya, Lanjutkan', isDanger = true }) {
            return new Promise((resolve) => {
                confirmResolver = resolve;
                document.getElementById('aiConfirmTitle').textContent = title;
                document.getElementById('aiConfirmMessage').textContent = message;
                
                const iconContainer = document.getElementById('aiConfirmIcon');
                if (icon) {
                    iconContainer.innerHTML = icon;
                } else {
                    iconContainer.innerHTML = isDanger ? SVG_ICONS.trash : SVG_ICONS.warning;
                }
                
                const submitBtn = document.getElementById('aiConfirmSubmitBtn');
                submitBtn.textContent = confirmText;
                if (isDanger) {
                    submitBtn.style.background = 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)';
                    submitBtn.style.boxShadow = '0 4px 14px rgba(244, 63, 94, 0.3)';
                } else {
                    submitBtn.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
                    submitBtn.style.boxShadow = '0 4px 14px rgba(34, 197, 94, 0.3)';
                }

                document.getElementById('aiConfirmModal').classList.add('active');
            });
        }

        function closeConfirmModal(result) {
            document.getElementById('aiConfirmModal').classList.remove('active');
            if (confirmResolver) {
                confirmResolver(result);
                confirmResolver = null;
            }
        }

        /* Custom Toast Notification System */
        function showToast(message, type = 'info', icon = '') {
            const container = document.getElementById('aiToastContainer');
            const toast = document.createElement('div');
            toast.className = `ai-toast-item toast-${type}`;
            
            let defaultIcon = SVG_ICONS.info;
            if (type === 'success') defaultIcon = SVG_ICONS.success;
            if (type === 'error') defaultIcon = SVG_ICONS.error;
            if (type === 'warning') defaultIcon = SVG_ICONS.warning;
            
            toast.innerHTML = `
                <span style="display:inline-flex; align-items:center;">${icon || defaultIcon}</span>
                <span style="flex:1; font-size:12.5px; font-weight:500;">${escapeHtml(message)}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        function getEmptyStateHTML() {
            return `
                <div class="chat-empty-wrapper" id="emptyState">
                    <div class="empty-hero-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8.01" y2="16"/><line x1="16" y1="16" x2="16.01" y2="16"/></svg>
                    </div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin:0 0 6px 0;">Asisten AI Ekscoder</h3>
                </div>
            `;
        }

        function sortConversations() {
            activeConversations.sort((a, b) => {
                const pinA = a.is_pinned ? 1 : 0;
                const pinB = b.is_pinned ? 1 : 0;
                if (pinA !== pinB) {
                    return pinB - pinA;
                }
                return new Date(b.updated_at || 0) - new Date(a.updated_at || 0);
            });
        }

        async function loadConversations() {
            try {
                const res = await fetch("{{ route('admin.ai-chat.conversations.index') }}");
                const json = await res.json();
                if (json.success) {
                    activeConversations = json.data;
                    sortConversations();
                    renderThreadList();
                    if (activeConversations.length > 0 && !currentConversationId) {
                        selectConversation(activeConversations[0].id);
                    } else {
                        updateHeaderPinButton();
                    }
                }
            } catch (e) {
                console.error('Error loading conversations:', e);
            }
        }

        function renderThreadList() {
            const listEl = document.getElementById('chatThreadList');
            if (activeConversations.length === 0) {
                listEl.innerHTML = `<div style="text-align:center; color:var(--text-muted); font-size:12px; padding:20px 0;">Belum ada percakapan.</div>`;
                return;
            }

            listEl.innerHTML = activeConversations.map(conv => `
                <div class="thread-item ${conv.id === currentConversationId ? 'active' : ''} ${conv.is_pinned ? 'pinned' : ''}" onclick="selectConversation(${conv.id})">
                    <span style="display:inline-flex; align-items:center; opacity:0.8;">${SVG_ICONS.chat}</span>
                    <span class="thread-title" title="${escapeHtml(conv.title)}">${escapeHtml(conv.title)}</span>
                    ${conv.is_pinned ? `<span class="thread-pin-badge" title="Percakapan Disematkan">${SVG_ICONS.pinFilled}</span>` : ''}
                    <div class="thread-actions" onclick="event.stopPropagation()">
                        <button class="btn-thread-action btn-pin ${conv.is_pinned ? 'is-pinned' : ''}" title="${conv.is_pinned ? 'Lepas Sematan' : 'Sematkan Percakapan'}" onclick="togglePinConversation(${conv.id}, event)">
                            ${conv.is_pinned ? SVG_ICONS.pinFilled : SVG_ICONS.pin}
                        </button>
                        <button class="btn-thread-action btn-delete" title="Hapus Percakapan" onclick="deleteConversation(${conv.id})">
                            ${SVG_ICONS.trash}
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function filterThreads() {
            const query = document.getElementById('searchThreadInput').value.toLowerCase();
            const items = document.querySelectorAll('.thread-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }

        function createNewChat() {
            currentConversationId = null;
            document.getElementById('activeChatTitle').value = 'Percakapan Baru';
            document.getElementById('chatMessagesContainer').innerHTML = getEmptyStateHTML();
            renderThreadList();
            updateHeaderPinButton();
            const inputEl = document.getElementById('chatInput');
            if (inputEl) inputEl.focus();
        }

        async function selectConversation(id) {
            currentConversationId = id;
            const conv = activeConversations.find(c => c.id === id);
            if (conv) {
                document.getElementById('activeChatTitle').value = conv.title;
                if (conv.model) {
                    document.getElementById('modelSelect').value = conv.model;
                }
            }
            renderThreadList();
            updateHeaderPinButton();
            await loadMessages(id);
        }

        async function togglePinConversation(id, event) {
            if (event) {
                event.stopPropagation();
            }
            const conv = activeConversations.find(c => c.id === id);
            if (!conv) return;

            const newPinState = !conv.is_pinned;
            conv.is_pinned = newPinState;
            sortConversations();
            renderThreadList();
            updateHeaderPinButton();

            try {
                const res = await fetch(`/admin/ai-chat/conversations/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ is_pinned: newPinState })
                });
                const json = await res.json();
                if (json.success) {
                    showToast(newPinState ? 'Percakapan disematkan ke atas' : 'Sematan percakapan dilepas', 'success');
                } else {
                    throw new Error(json.message || 'Gagal mengubah status sematan');
                }
            } catch (e) {
                console.error('Error toggling pin:', e);
                showToast('Gagal mengubah status sematan', 'error');
                conv.is_pinned = !newPinState;
                sortConversations();
                renderThreadList();
                updateHeaderPinButton();
            }
        }

        function togglePinCurrentConversation() {
            if (currentConversationId) {
                togglePinConversation(currentConversationId);
            }
        }

        function updateHeaderPinButton() {
            const btn = document.getElementById('btnPinCurrentChat');
            if (!btn) return;
            if (!currentConversationId) {
                btn.style.display = 'none';
                return;
            }
            // btn.style.display = 'inline-flex';
            const conv = activeConversations.find(c => c.id === currentConversationId);
            // if (conv && conv.is_pinned) {
            //     btn.className = 'btn-top-action is-pinned';
            //     btn.innerHTML = `${SVG_ICONS.pinFilled} <span style="font-size:11.5px; margin-left:2px;">Disematkan</span>`;
            //     btn.title = 'Lepas Sematan Percakapan ini';
            // } else {
            //     btn.className = 'btn-top-action';
            //     btn.innerHTML = `${SVG_ICONS.pin} <span style="font-size:11.5px; margin-left:2px;">Sematkan</span>`;
            //     btn.title = 'Sematkan Percakapan ini ke atas';
            // }
        }

        async function saveCurrentThreadTitle(newTitle) {
            if (!currentConversationId || !newTitle.trim()) return;
            try {
                await fetch(`/admin/ai-chat/conversations/${currentConversationId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ title: newTitle.trim() })
                });
                loadConversations();
            } catch (e) {
                console.error('Error saving thread title:', e);
            }
        }

        async function loadMessages(conversationId) {
            const container = document.getElementById('chatMessagesContainer');
            container.innerHTML = `<div style="text-align:center; color:var(--text-muted); padding:30px; font-size:13px;">Memuat riwayat pesan...</div>`;

            try {
                const res = await fetch(`/admin/ai-chat/conversations/${conversationId}/messages`);
                const json = await res.json();
                if (json.success) {
                    const messages = json.data;
                    if (messages.length === 0) {
                        container.innerHTML = getEmptyStateHTML();
                    } else {
                        container.innerHTML = '';
                        messages.forEach(msg => {
                            appendMessageBubble(msg.role, msg.content, msg.images);
                        });
                    }
                    scrollToBottom();
                }
            } catch (e) {
                console.error('Error loading messages:', e);
                container.innerHTML = `<div style="color:var(--rose, #f43f5e); padding:20px; text-align:center;">Gagal memuat riwayat pesan.</div>`;
            }
        }

        /* Image Attachment & Lightbox Functions */
        function handleImageFileSelect(e) {
            const files = e.target.files;
            if (!files || !files.length) return;
            for (let i = 0; i < files.length; i++) {
                readAndAddImageFile(files[i]);
            }
            e.target.value = '';
        }

        function readAndAddImageFile(file) {
            if (file.size > 10 * 1024 * 1024) {
                showToast('Ukuran gambar maksimal 10MB.', 'error');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(evt) {
                attachedImages.push(evt.target.result);
                renderAttachedImagesPreview();
                showToast('Gambar siap dikirim!', 'success');
            };
            reader.readAsDataURL(file);
        }

        function renderAttachedImagesPreview() {
            const bar = document.getElementById('imageAttachmentBar');
            const box = document.getElementById('chatInputBox');
            if (!attachedImages.length) {
                bar.style.display = 'none';
                if (box) {
                    box.style.borderTopLeftRadius = '12px';
                    box.style.borderTopRightRadius = '12px';
                }
                return;
            }
            bar.style.display = 'flex';
            if (box) {
                box.style.borderTopLeftRadius = '0';
                box.style.borderTopRightRadius = '0';
            }
            bar.innerHTML = attachedImages.map((img, idx) => `
                <div class="image-attachment-item">
                    <img src="${img}" alt="Attachment">
                    <button type="button" class="btn-remove-attachment" onclick="removeAttachedImage(${idx})" title="Hapus Gambar">✕</button>
                </div>
            `).join('');
        }

        function removeAttachedImage(idx) {
            attachedImages.splice(idx, 1);
            renderAttachedImagesPreview();
        }

        function openImageLightbox(src) {
            const modal = document.getElementById('imageLightboxModal');
            const img = document.getElementById('lightboxImg');
            if (modal && img) {
                img.src = src;
                modal.classList.add('active');
            }
        }

        function closeImageLightbox() {
            const modal = document.getElementById('imageLightboxModal');
            if (modal) {
                modal.classList.remove('active');
            }
        }

        async function deleteConversation(id) {
            const confirmed = await customConfirm({
                title: 'Hapus Percakapan',
                message: 'Apakah Anda yakin ingin menghapus percakapan ini? Tindakan ini tidak dapat dibatalkan.',
                confirmText: 'Ya, Hapus',
                isDanger: true
            });

            if (!confirmed) return;

            try {
                const res = await fetch(`/admin/ai-chat/conversations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const json = await res.json();
                if (json.success) {
                    showToast('Percakapan berhasil dihapus', 'success');
                    
                    // Immediately remove deleted thread from local state
                    activeConversations = activeConversations.filter(c => c.id !== id);

                    if (currentConversationId === id) {
                        if (activeConversations.length > 0) {
                            selectConversation(activeConversations[0].id);
                        } else {
                            createNewChat();
                        }
                    } else {
                        renderThreadList();
                        updateHeaderPinButton();
                    }
                }
            } catch (e) {
                showToast('Gagal menghapus percakapan', 'error');
            }
        }

        async function clearCurrentChatMessages() {
            if (!currentConversationId) return;

            const confirmed = await customConfirm({
                title: 'Bersihkan Pesan',
                message: 'Apakah Anda yakin ingin membersihkan seluruh riwayat pesan di percakapan ini?',
                confirmText: 'Bersihkan',
                isDanger: true
            });

            if (!confirmed) return;

            try {
                const res = await fetch(`/admin/ai-chat/conversations/${currentConversationId}/messages`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const json = await res.json();
                if (json.success) {
                    showToast('Riwayat pesan berhasil dibersihkan', 'success');
                    document.getElementById('chatMessagesContainer').innerHTML = getEmptyStateHTML();
                }
            } catch (e) {
                showToast('Gagal membersihkan pesan', 'error');
            }
        }

        function usePromptChip(promptText) {
            document.getElementById('chatInput').value = promptText;
            autoResizeTextarea(document.getElementById('chatInput'));
            sendMessage();
        }

        function handleInputKeydown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        }

        function autoResizeTextarea(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 140) + 'px';
        }

        function appendMessageBubble(role, content = '', images = []) {
            const container = document.getElementById('chatMessagesContainer');
            const emptyEl = container.querySelector('.chat-empty-wrapper');
            if (emptyEl) {
                container.innerHTML = '';
            }

            const row = document.createElement('div');
            row.className = `chat-msg-row ${role}`;

            const avatarIcon = role === 'user' ? SVG_ICONS.user : SVG_ICONS.bot;
            const parsedHTML = role === 'assistant' ? formatAssistantMarkdown(content) : (content ? escapeHtml(content).replace(/\n/g, '<br>') : '');

            let imagesHtml = '';
            if (images && images.length) {
                imagesHtml = `<div class="chat-msg-images-grid">` +
                    images.map(img => `<img src="${img}" class="chat-msg-image-thumb" onclick="openImageLightbox('${img}')" title="Klik untuk memperbesar gambar" alt="Attachment">`).join('') +
                    `</div>`;
            }

            row.innerHTML = `
                <div class="chat-msg-avatar">${avatarIcon}</div>
                <div class="chat-msg-bubble">
                    ${imagesHtml}
                    <div class="bubble-content-text">${parsedHTML}</div>
                    ${role === 'assistant' && content ? `
                        <div class="bubble-footer-actions">
                            <button class="btn-bubble-action" onclick="copyTextToClipboard(this)">${SVG_ICONS.copy} Salin Balasan</button>
                        </div>
                    ` : ''}
                </div>
            `;

            container.appendChild(row);
            formatCodeBlocksInElement(row);
            scrollToBottom();
            return row.querySelector('.bubble-content-text');
        }

        function formatAssistantMarkdown(text) {
            if (!text) return '';
            try {
                return marked.parse(text);
            } catch (e) {
                return escapeHtml(text).replace(/\n/g, '<br>');
            }
        }

        function formatCodeBlocksInElement(parentEl) {
            const pres = parentEl.querySelectorAll('pre');
            pres.forEach(pre => {
                if (pre.parentElement && pre.parentElement.classList.contains('code-block-card')) return;

                const codeEl = pre.querySelector('code');
                let lang = 'code';
                if (codeEl) {
                    const classMatch = codeEl.className.match(/language-(\w+)/);
                    if (classMatch) lang = classMatch[1];
                }

                if (codeEl) {
                    try {
                        hljs.highlightElement(codeEl);
                    } catch (err) {}
                }

                const card = document.createElement('div');
                card.className = 'code-block-card';
                card.innerHTML = `
                    <div class="code-block-header">
                        <span>${escapeHtml(lang)}</span>
                        <button class="btn-copy-code" onclick="copyCodeSnippet(this)">Copy Code</button>
                    </div>
                `;

                pre.parentNode.insertBefore(card, pre);
                card.appendChild(pre);
            });
        }

        function copyCodeSnippet(btn) {
            const card = btn.closest('.code-block-card');
            const code = card.querySelector('code')?.innerText || card.querySelector('pre')?.innerText;
            if (code) {
                navigator.clipboard.writeText(code);
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy Code', 2000);
            }
        }

        function copyTextToClipboard(btn) {
            const bubble = btn.closest('.chat-msg-bubble');
            const text = bubble.querySelector('.bubble-content-text')?.innerText || bubble.innerText;
            if (text) {
                navigator.clipboard.writeText(text);
                const originalText = btn.innerHTML;
                btn.innerHTML = '✅ Tersalin!';
                setTimeout(() => btn.innerHTML = originalText, 2000);
            }
        }

        async function sendMessage() {
            if (isSending) return;

            const inputEl = document.getElementById('chatInput');
            const userText = inputEl.value.trim();
            if (!userText && attachedImages.length === 0) return;

            const currentImages = [...attachedImages];
            attachedImages = [];
            renderAttachedImagesPreview();

            inputEl.value = '';
            inputEl.style.height = 'auto';
            isSending = true;

            const selectedModel = document.getElementById('modelSelect').value;

            // Create new conversation in DB on first message if draft mode
            if (!currentConversationId) {
                try {
                    const firstLine = userText ? userText.split('\n')[0].trim() : 'Analisa Gambar';
                    const titleText = firstLine.length > 80 ? firstLine.substring(0, 80) + '...' : (firstLine || 'Percakapan Baru');
                    const res = await fetch("{{ route('admin.ai-chat.conversations.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: titleText,
                            model: selectedModel
                        })
                    });
                    const json = await res.json();
                    if (json.success) {
                        currentConversationId = json.data.id;
                        document.getElementById('activeChatTitle').value = json.data.title;
                        await loadConversations();
                    } else {
                        throw new Error('Gagal membuat thread percakapan.');
                    }
                } catch (e) {
                    showToast('Gagal memulai percakapan baru: ' + e.message, 'error');
                    isSending = false;
                    return;
                }
            }

            appendMessageBubble('user', userText, currentImages);

            const assistantBubble = appendMessageBubble('assistant', '');
            assistantBubble.innerHTML = `
                <div class="dots-typing">
                    <div class="dot-bounce"></div>
                    <div class="dot-bounce"></div>
                    <div class="dot-bounce"></div>
                </div>
            `;

            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;

            try {
                const response = await fetch("{{ route('admin.ai-chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        conversation_id: currentConversationId,
                        message: userText,
                        images: currentImages,
                        model: selectedModel
                    })
                });

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let accumulatedText = '';
                let isFirstChunk = true;
                let sseBuffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    sseBuffer += decoder.decode(value, { stream: true });
                    const lines = sseBuffer.split('\n');
                    sseBuffer = lines.pop() || '';

                    for (const line of lines) {
                        const trimmedLine = line.trim();
                        if (trimmedLine.startsWith('data: ')) {
                            const dataPayload = trimmedLine.substring(6).trim();
                            if (dataPayload === '[DONE]') {
                                break;
                            }
                            try {
                                const parsed = JSON.parse(dataPayload);
                                if (parsed.chunk) {
                                    if (isFirstChunk) {
                                        assistantBubble.innerHTML = '';
                                        isFirstChunk = false;
                                    }
                                    accumulatedText += parsed.chunk;
                                    assistantBubble.innerHTML = formatAssistantMarkdown(accumulatedText);
                                    formatCodeBlocksInElement(assistantBubble.parentElement);
                                    scrollToBottom();
                                } else if (parsed.error) {
                                    assistantBubble.innerHTML = `<span style="color:var(--rose, #f43f5e);">${escapeHtml(parsed.error)}</span>`;
                                }
                            } catch (err) {
                                // Ignore non-JSON stream data
                            }
                        }
                    }
                }

                if (!accumulatedText && isFirstChunk) {
                    assistantBubble.innerHTML = `<div style="background:var(--rose-soft, rgba(244, 63, 94, 0.12)); border:1px solid rgba(244, 63, 94, 0.3); padding:10px 14px; border-radius:10px; color:var(--rose, #f43f5e); font-size:12.5px;">
                        <strong>⚠️ 9Router Error / Invalid API Key:</strong><br>
                        9Router tidak mengembalikan respon untuk model <strong>${escapeHtml(selectedModel)}</strong>.<br>
                        Silakan masukkan <strong>API Key</strong> 9Router Anda melalui tombol <button onclick="toggleSettingsModal()" style="background:var(--rose, #f43f5e); color:#fff; border:none; border-radius:4px; padding:2px 8px; font-size:11px; cursor:pointer; margin-left:4px;">Pengaturan API</button>.
                    </div>`;
                }

                loadConversations();

            } catch (error) {
                assistantBubble.innerHTML = `<span style="color:var(--rose, #f43f5e);">Gagal terhubung ke 9Router: ${escapeHtml(error.message)}</span>`;
            } finally {
                isSending = false;
                sendBtn.disabled = false;
                scrollToBottom();
            }
        }

        function scrollToBottom() {
            const container = document.getElementById('chatMessagesContainer');
            container.scrollTop = container.scrollHeight;
        }

        function toggleSettingsModal() {
            const modal = document.getElementById('settingsModal');
            modal.classList.toggle('active');
            if (modal.classList.contains('active')) {
                load9RouterModels();
            }
        }

        async function load9RouterModels() {
            try {
                const res = await fetch("{{ route('admin.ai-chat.models') }}");
                const json = await res.json();
                if (json.success && json.models && json.models.length > 0) {
                    // 1. Update Header Chat Model Dropdown
                    const selectEl = document.getElementById('modelSelect');
                    if (selectEl) {
                        const currentVal = selectEl.value;
                        selectEl.innerHTML = json.models.map(m => `<option value="${escapeHtml(m)}">${escapeHtml(m)}</option>`).join('');
                        if (json.models.includes(currentVal)) {
                            selectEl.value = currentVal;
                        } else if (json.models.length > 0) {
                            selectEl.value = json.models[0];
                        }
                    }

                    // 2. Update Settings Modal Default Model Dropdown
                    const modalSelectEl = document.getElementById('settingDefaultModel');
                    if (modalSelectEl) {
                        const currentModalVal = modalSelectEl.value;
                        let optionsHtml = '';
                        if (currentModalVal && !json.models.includes(currentModalVal)) {
                            optionsHtml += `<option value="${escapeHtml(currentModalVal)}">${escapeHtml(currentModalVal)} (Custom/Saat Ini)</option>`;
                        }
                        optionsHtml += json.models.map(m => `<option value="${escapeHtml(m)}">${escapeHtml(m)}</option>`).join('');
                        modalSelectEl.innerHTML = optionsHtml;
                        if (currentModalVal && (json.models.includes(currentModalVal) || optionsHtml.includes(currentModalVal))) {
                            modalSelectEl.value = currentModalVal;
                        } else if (json.models.length > 0) {
                            modalSelectEl.value = json.models[0];
                        }
                    }
                }
            } catch (e) {
                console.error('Error fetching 9Router models:', e);
            }
        }

        async function saveSettings(e) {
            e.preventDefault();
            const baseUrl = document.getElementById('settingBaseUrl').value.trim();
            const apiKey = document.getElementById('settingApiKey').value.trim();
            const defaultModel = document.getElementById('settingDefaultModel').value.trim();
            const systemPrompt = document.getElementById('settingSystemPrompt').value.trim();

            try {
                const res = await fetch("{{ route('admin.ai-chat.settings.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        base_url: baseUrl,
                        api_key: apiKey,
                        default_model: defaultModel,
                        system_prompt: systemPrompt
                    })
                });
                const json = await res.json();
                if (json.success) {
                    showToast('Pengaturan 9Router berhasil disimpan!', 'success');
                    toggleSettingsModal();
                    test9RouterConnection();
                }
            } catch (err) {
                showToast('Gagal menyimpan pengaturan.', 'error');
            }
        }

        async function test9RouterConnection() {
            const dot = document.getElementById('connStatusDot');
            const text = document.getElementById('connStatusText');
            dot.className = 'status-dot checking';
            text.textContent = 'Memeriksa...';

            try {
                const res = await fetch("{{ route('admin.ai-chat.test-connection') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const json = await res.json();
                if (json.success) {
                    dot.className = 'status-dot';
                    text.textContent = '9Router Online';
                    load9RouterModels();
                } else {
                    dot.className = 'status-dot offline';
                    text.textContent = '9Router Offline / Invalid Key';
                }
            } catch (e) {
                dot.className = 'status-dot offline';
                text.textContent = '9Router Offline';
            }
        }

        async function test9RouterConnectionInModal() {
            const resultDiv = document.getElementById('modalTestResult');
            resultDiv.style.display = 'block';
            resultDiv.style.background = 'var(--amber-soft, rgba(245, 158, 11, 0.15))';
            resultDiv.style.color = 'var(--amber, #f59e0b)';
            resultDiv.textContent = 'Memeriksa koneksi 9Router...';

            try {
                const res = await fetch("{{ route('admin.ai-chat.test-connection') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const json = await res.json();
                if (json.success) {
                    resultDiv.style.background = 'var(--green-soft, rgba(34, 197, 94, 0.15))';
                    resultDiv.style.color = 'var(--green, #22c55e)';
                    resultDiv.textContent = '✅ ' + json.message;
                    showToast('Terhubung dengan 9Router Gateway!', 'success');
                    load9RouterModels();
                } else {
                    resultDiv.style.background = 'var(--rose-soft, rgba(244, 63, 94, 0.15))';
                    resultDiv.style.color = 'var(--rose, #f43f5e)';
                    resultDiv.textContent = '❌ ' + json.message;
                    showToast('Koneksi 9Router Gagal', 'error');
                }
            } catch (e) {
                resultDiv.style.background = 'var(--rose-soft, rgba(244, 63, 94, 0.15))';
                resultDiv.style.color = 'var(--rose, #f43f5e)';
                resultDiv.textContent = '❌ Gagal terhubung: ' + e.message;
                showToast('Gagal terhubung ke 9Router', 'error');
            }
        }

        function changeModel(newModel) {
            if (currentConversationId) {
                fetch(`/admin/ai-chat/conversations/${currentConversationId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ model: newModel })
                });
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</x-admin-layout>
