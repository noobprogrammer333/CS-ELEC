<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NoteAI Lessons</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
    <link rel="stylesheet" href="static/css/style.css">
  </head>
  <body class="app-body">
    <div class="app-shell">
      <aside id="appSidebar" class="app-sidebar glass-panel">
        <div class="sidebar-brand">
          <div class="brand-icon neon-glow">
            <i data-lucide="settings"></i>
          </div>
          <h2 class="sidebar-label">System</h2>
        </div>

        <button id="sidebarToggle" class="sidebar-toggle neon-glow" type="button" aria-label="Toggle sidebar">
          <i id="sidebarToggleIcon" data-lucide="panel-left-close"></i>
        </button>

        <div class="sidebar-expanded">
          <div class="sidebar-section">
            <label class="sidebar-heading">System Status</label>
            <div class="status-card">
              <span class="status-dot"></span>
              <span>NoteAI Brain: Online</span>
            </div>
          </div>

          <div class="sidebar-section">
            <label class="sidebar-heading" for="selectedModel">Active AI Mode</label>
            <div class="select-shell">
              <select id="selectedModel" class="form-select">
                <option value="note-retrieval">Supabase Note Retrieval</option>
                <option value="local-nlp">NLTK + spaCy NLP</option>
              </select>
              <i data-lucide="chevron-down"></i>
            </div>
          </div>
        </div>

        <div class="sidebar-collapsed">
          <span class="status-dot" title="System Online"></span>
          <i data-lucide="brain-circuit"></i>
        </div>

        <button id="clearNotesBtn" class="sidebar-danger" type="button">
          <i data-lucide="trash-2"></i>
          <span class="sidebar-label">Clear All Notes</span>
        </button>
      </aside>

      <main class="app-main">
        <header class="app-header">
          <div>
            <h1 id="viewTitle" class="app-title neon-text">📝 NoteAI Assistant</h1>
            <div class="ready-line">
              <span class="status-dot"></span>
              <span id="viewSubtitle">Ready to take notes</span>
            </div>
          </div>

          <button id="viewToggleBtn" class="round-action glass-panel neon-glow" type="button" aria-label="Toggle view">
            <i id="viewToggleIcon" data-lucide="message-square"></i>
          </button>
        </header>

        <div class="app-content">
          <section id="notesView" class="view-panel is-active">
            <div class="note-composer-wrap">
              <div class="composer-glow"></div>
              <form id="noteForm" class="note-composer glass-panel">
                <input
                  id="noteTitle"
                  class="title-input"
                  type="text"
                  placeholder="Optional note title..."
                  autocomplete="off"
                >
                <textarea
                  id="noteContent"
                  class="note-textarea custom-scrollbar"
                  placeholder="What did you learn today? Start typing or use the mic..."
                ></textarea>
                <div class="composer-actions">
                  <button id="noteVoiceBtn" class="dictation-button" type="button">
                    <i data-lucide="mic"></i>
                    <span>Voice Dictation</span>
                  </button>
                  <button class="save-note-button" type="submit">
                    <i data-lucide="plus"></i>
                    Save Typed Note
                  </button>
                </div>
                <div id="noteVoiceStatus" class="voice-status">Speech becomes text inside the note.</div>
                <div id="noteStorageNotice" class="alert alert-warning d-none mt-3"></div>
              </form>
            </div>

            <div class="note-dashboard">
              <section class="glass-panel p-4 nlp-panel">
                <div class="panel-heading">
                  <div>
                    <h2 class="h5 mb-1">Saved Note NLP Output</h2>
                    <small>Tokenization, lemmatization, sentiment, and keywords</small>
                  </div>
                </div>
                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <div class="metric-card">
                      <span class="metric-label">Sentiment</span>
                      <strong id="sentimentResult">Waiting</strong>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="metric-card">
                      <span class="metric-label">Classification</span>
                      <strong id="classificationResult">Waiting</strong>
                    </div>
                  </div>
                </div>
                <h3 class="h6">Tokenized Note</h3>
                <div id="tokenList" class="token-area mb-3 custom-scrollbar">Save a note to see tokens.</div>
                <h3 class="h6">Lemmatized Terms</h3>
                <div id="lemmaList" class="token-area mb-3 custom-scrollbar">Save a note to see lemmas.</div>
                <h3 class="h6">Extracted Keywords</h3>
                <div id="keywordList" class="token-area custom-scrollbar">Save a note to see keywords.</div>
              </section>

              <section class="notebook-panel">
                <div class="notebook-heading">
                  <div>
                    <i data-lucide="notebook"></i>
                    <span>Your Notebook</span>
                  </div>
                  <button id="refreshNotesBtn" class="shortcut-button" type="button">Refresh Notes</button>
                </div>
                <div id="notesNotice" class="alert alert-warning d-none"></div>
                <div id="notesList" class="notes-list custom-scrollbar"></div>
              </section>
            </div>
          </section>

          <section id="chatView" class="view-panel">
            <div class="chat-layout">
              <div class="chat-column">
                <div id="chatBox" class="chat-box custom-scrollbar" aria-live="polite">
                  <div class="empty-chat">
                    <div class="empty-chat-icon glass-panel neon-glow">
                      <i data-lucide="brain-circuit"></i>
                    </div>
                    <p>Ready to assist you with your knowledge database...</p>
                  </div>
                </div>

                <div class="shortcut-row">
                  <button class="shortcut-button" data-shortcut="Can you provide a concise summary of all my notes?">Summarize Notes</button>
                  <button class="shortcut-button" data-shortcut="What are the most frequent keywords in my notebook?">Find Keywords</button>
                  <button class="shortcut-button" data-shortcut="Can you explain the main concepts from my most recent note?">Explain Lesson</button>
                  <button class="shortcut-button" data-shortcut="Look through my notes and analyze the main topics discussed.">Analyze Topic</button>
                  <button class="shortcut-button" data-shortcut="Give me 3 review questions based on my saved notes.">Review Notes</button>
                </div>

                <form id="chatForm" class="chat-input-shell glass-panel">
                  <input
                    id="userInput"
                    type="text"
                    placeholder="Ask NoteAI anything..."
                    autocomplete="off"
                  >
                  <button id="voiceBtn" class="icon-action" type="button" aria-label="Voice question">
                    <i data-lucide="mic"></i>
                  </button>
                  <button class="send-action" type="submit" aria-label="Send question">
                    <i data-lucide="send"></i>
                  </button>
                </form>
                <div class="chat-meta">
                  <span id="voiceStatus">Ask with text or voice.</span>
                  <button id="speakLastBtn" class="shortcut-button" type="button">Speak Answer</button>
                </div>
              </div>

              <aside class="assistant-side">
                <section class="glass-panel p-4 mb-4">
                  <h2 class="h5 mb-1">Matched Notes</h2>
                  <small>Sources used by the assistant answer</small>
                  <div id="matchedNotes" class="matched-notes-list custom-scrollbar mt-3">
                    <p class="text-muted mb-0">Ask a question to see matching notes.</p>
                  </div>
                </section>

                <section class="glass-panel p-4">
                  <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <div>
                      <h2 class="h5 mb-1">Q&A History</h2>
                      <small>Stored after note retrieval</small>
                    </div>
                    <button id="refreshHistoryBtn" class="shortcut-button" type="button">Refresh</button>
                  </div>
                  <div id="historyNotice" class="alert alert-warning d-none"></div>
                  <div class="table-responsive custom-scrollbar history-table-wrap">
                    <table class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Question</th>
                          <th>Answer</th>
                          <th>Matched</th>
                        </tr>
                      </thead>
                      <tbody id="historyTable">
                        <tr><td colspan="4" class="text-muted">No history loaded yet.</td></tr>
                      </tbody>
                    </table>
                  </div>
                </section>
              </aside>
            </div>
          </section>
        </div>
      </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="static/js/script.js"></script>
  </body>
</html>
