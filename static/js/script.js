const chatBox = document.getElementById("chatBox");
const chatForm = document.getElementById("chatForm");
const userInput = document.getElementById("userInput");
const voiceBtn = document.getElementById("voiceBtn");
const voiceStatus = document.getElementById("voiceStatus");
const speakLastBtn = document.getElementById("speakLastBtn");
const noteForm = document.getElementById("noteForm");
const noteTitle = document.getElementById("noteTitle");
const noteContent = document.getElementById("noteContent");
const noteVoiceBtn = document.getElementById("noteVoiceBtn");
const noteVoiceStatus = document.getElementById("noteVoiceStatus");
const clearNotesBtn = document.getElementById("clearNotesBtn");
const refreshNotesBtn = document.getElementById("refreshNotesBtn");
const notesList = document.getElementById("notesList");
const notesNotice = document.getElementById("notesNotice");
const noteStorageNotice = document.getElementById("noteStorageNotice");
const refreshHistoryBtn = document.getElementById("refreshHistoryBtn");
const historyTable = document.getElementById("historyTable");
const historyNotice = document.getElementById("historyNotice");
const matchedNotes = document.getElementById("matchedNotes");

let lastBotResponse = "";

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function renderChips(items, containerId, className = "token-chip") {
  const container = document.getElementById(containerId);
  if (!items || items.length === 0) {
    container.innerHTML = '<span class="text-muted">None detected.</span>';
    return;
  }

  container.innerHTML = items
    .map((item) => `<span class="${className}">${escapeHtml(item)}</span>`)
    .join("");
}

function addChatMessage(role, text) {
  const message = document.createElement("div");
  message.className = `chat-message ${role}`;
  message.innerHTML = `<small>${role === "user" ? "You" : "Bot"}</small>${escapeHtml(text)}`;
  chatBox.appendChild(message);
  chatBox.scrollTop = chatBox.scrollHeight;
}

function updateAnalysisPanel(analysis) {
  document.getElementById("sentimentResult").textContent =
    `${analysis.sentiment} (${analysis.sentiment_score})`;
  document.getElementById("classificationResult").textContent = analysis.classification;
  renderChips(analysis.tokens, "tokenList");
  renderChips(analysis.lemmas, "lemmaList");
  renderChips(analysis.keywords, "keywordList", "token-chip keyword-chip");
}

function showNotice(element, message) {
  if (!element) return;
  element.classList.toggle("d-none", !message);
  element.textContent = message || "";
}

function startVoiceInput(targetElement, statusElement, triggerButton, append = false) {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    statusElement.textContent = "Voice input is not supported by this browser.";
    return;
  }

  const recognition = new SpeechRecognition();
  recognition.lang = "en-US";
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  recognition.onstart = () => {
    statusElement.textContent = "Listening... speak now.";
    triggerButton.disabled = true;
  };

  recognition.onresult = (event) => {
    const transcript = event.results[0][0].transcript;
    targetElement.value = append && targetElement.value.trim()
      ? `${targetElement.value.trim()} ${transcript}`
      : transcript;
    statusElement.textContent = `Captured: ${transcript}`;
  };

  recognition.onerror = (event) => {
    statusElement.textContent = `Voice error: ${event.error}`;
  };

  recognition.onend = () => {
    triggerButton.disabled = false;
  };

  recognition.start();
}

async function saveNote(title, content) {
  const response = await fetch("api/notes.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ title, content }),
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.error || data.storage?.warning || "Unable to save note.");
  }

  updateAnalysisPanel(data.analysis);
  showNotice(noteStorageNotice, data.storage?.warning || "");
  await loadNotes();
}

function renderNotes(notes) {
  if (!notes || notes.length === 0) {
    notesList.innerHTML = '<p class="text-muted mb-0">No saved notes yet. Create one above.</p>';
    return;
  }

  notesList.innerHTML = notes
    .map((note) => {
      const keywords = Array.isArray(note.keywords) ? note.keywords : [];
      const lemmas = Array.isArray(note.lemmas) ? note.lemmas : [];
      return `
        <div class="col-md-6 col-xl-4">
          <article class="note-card h-100">
            <h3 class="h5">${escapeHtml(note.title || "Untitled Note")}</h3>
            <p>${escapeHtml(note.content || "")}</p>
            <div class="mb-2">
              ${keywords.slice(0, 5).map((keyword) => `<span class="token-chip keyword-chip">${escapeHtml(keyword)}</span>`).join("")}
            </div>
            <small class="text-muted d-block">Sentiment: ${escapeHtml(note.sentiment || "N/A")}</small>
            <small class="text-muted d-block">Class: ${escapeHtml(note.classification || "N/A")}</small>
            <small class="text-muted d-block">Lemmas: ${escapeHtml(lemmas.slice(0, 6).join(", "))}</small>
          </article>
        </div>
      `;
    })
    .join("");
}

async function loadNotes() {
  const response = await fetch("api/notes.php");
  const data = await response.json();
  showNotice(notesNotice, data.warning || "");
  renderNotes(data.notes || []);
}

async function sendChatMessage(message) {
  addChatMessage("user", message);

  const response = await fetch("api/chat.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ message }),
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.error || "Unable to process message.");
  }

  lastBotResponse = data.bot_response;
  addChatMessage("bot", data.bot_response);
  if (data.storage && data.storage.warning) {
    addChatMessage("bot", `Storage notice: ${data.storage.warning}`);
  }
  renderMatchedNotes(data.matches || []);
  await loadHistory();
}

async function loadHistory() {
  const response = await fetch("api/history.php");
  const data = await response.json();
  const history = data.history || [];
  if (historyNotice) {
    historyNotice.classList.toggle("d-none", !data.warning);
    historyNotice.textContent = data.warning || "";
  }

  if (history.length === 0) {
    historyTable.innerHTML = '<tr><td colspan="5" class="text-muted">No assistant questions found.</td></tr>';
    return;
  }

  historyTable.innerHTML = history
    .map(
      (item) => `
        <tr>
          <td>${item.id}</td>
          <td>${escapeHtml(item.question || "")}</td>
          <td>${escapeHtml(item.answer || "")}</td>
          <td>${escapeHtml(Array.isArray(item.matched_note_ids) ? item.matched_note_ids.join(", ") : "")}</td>
          <td>${escapeHtml(item.created_at)}</td>
        </tr>
      `
    )
    .join("");
}

function speakText(text) {
  if (!("speechSynthesis" in window)) {
    alert("Text-to-speech is not supported by this browser.");
    return;
  }

  window.speechSynthesis.cancel();
  const utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = "en-US";
  utterance.rate = 1;
  window.speechSynthesis.speak(utterance);
}

function renderMatchedNotes(notes) {
  if (!notes || notes.length === 0) {
    matchedNotes.innerHTML = '<p class="text-muted mb-0">No matching notes found for this question.</p>';
    return;
  }

  matchedNotes.innerHTML = notes
    .map((note) => `
      <article class="matched-note mb-3">
        <h3 class="h6">${escapeHtml(note.title || "Untitled Note")}</h3>
        <p class="mb-2">${escapeHtml(note.content || "")}</p>
        <small class="text-muted">Score: ${escapeHtml(note.match_score || 0)}</small>
      </article>
    `)
    .join("");
}

noteForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const title = noteTitle.value.trim();
  const content = noteContent.value.trim();
  if (!content) {
    showNotice(noteStorageNotice, "Please write or dictate note content first.");
    return;
  }

  try {
    await saveNote(title, content);
    noteTitle.value = "";
    noteContent.value = "";
  } catch (error) {
    showNotice(noteStorageNotice, error.message);
  }
});

chatForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const message = userInput.value.trim();
  if (!message) return;

  userInput.value = "";
  try {
    await sendChatMessage(message);
  } catch (error) {
    addChatMessage("bot", error.message);
  }
});

noteVoiceBtn.addEventListener("click", () => {
  startVoiceInput(noteContent, noteVoiceStatus, noteVoiceBtn, true);
});

voiceBtn.addEventListener("click", () => {
  startVoiceInput(userInput, voiceStatus, voiceBtn, false);
});

speakLastBtn.addEventListener("click", () => {
  if (!lastBotResponse) {
    alert("Send a message first so there is a bot response to speak.");
    return;
  }
  speakText(lastBotResponse);
});

clearNotesBtn.addEventListener("click", async () => {
  if (!confirm("Clear all saved notes?")) return;
  await fetch("api/notes.php", { method: "DELETE" });
  await loadNotes();
});

refreshNotesBtn.addEventListener("click", loadNotes);

document.getElementById("clearHistoryBtn")?.addEventListener("click", async () => {
  if (!confirm("Clear all assistant question history?")) return;
  await fetch("api/history.php", { method: "DELETE" });
  chatBox.innerHTML = "";
  await loadHistory();
});

refreshHistoryBtn.addEventListener("click", loadHistory);

loadNotes();
loadHistory();
