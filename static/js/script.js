const chatBox = document.getElementById("chatBox");
const chatForm = document.getElementById("chatForm");
const userInput = document.getElementById("userInput");
const voiceBtn = document.getElementById("voiceBtn");
const voiceStatus = document.getElementById("voiceStatus");
const speakLastBtn = document.getElementById("speakLastBtn");
const clearHistoryBtn = document.getElementById("clearHistoryBtn");
const refreshHistoryBtn = document.getElementById("refreshHistoryBtn");
const historyTable = document.getElementById("historyTable");
const historyNotice = document.getElementById("historyNotice");
const analyzeBtn = document.getElementById("analyzeBtn");
const analyzerInput = document.getElementById("analyzerInput");
const analyzerOutput = document.getElementById("analyzerOutput");

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
  document.getElementById("responsePreview").textContent = analysis.response;
  renderChips(analysis.tokens, "tokenList");
  renderChips(analysis.keywords, "keywordList", "token-chip keyword-chip");
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
  updateAnalysisPanel(data.analysis);
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
    historyTable.innerHTML = '<tr><td colspan="6" class="text-muted">No chat records found.</td></tr>';
    return;
  }

  historyTable.innerHTML = history
    .map(
      (item) => `
        <tr>
          <td>${item.id}</td>
          <td>${escapeHtml(item.user_message)}</td>
          <td>${escapeHtml(item.bot_response)}</td>
          <td>${escapeHtml(item.sentiment)}</td>
          <td>${escapeHtml(item.classification)}</td>
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

function startVoiceInput() {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    voiceStatus.textContent = "Voice input is not supported by this browser.";
    return;
  }

  const recognition = new SpeechRecognition();
  recognition.lang = "en-US";
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  recognition.onstart = () => {
    voiceStatus.textContent = "Listening... speak now.";
    voiceBtn.disabled = true;
  };

  recognition.onresult = (event) => {
    const transcript = event.results[0][0].transcript;
    userInput.value = transcript;
    voiceStatus.textContent = `Captured: ${transcript}`;
  };

  recognition.onerror = (event) => {
    voiceStatus.textContent = `Voice error: ${event.error}`;
  };

  recognition.onend = () => {
    voiceBtn.disabled = false;
  };

  recognition.start();
}

function renderAnalyzerOutput(analysis) {
  analyzerOutput.innerHTML = `
    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <div class="metric-card">
          <span class="metric-label">Sentiment</span>
          <strong>${escapeHtml(analysis.sentiment)} (${analysis.sentiment_score})</strong>
        </div>
      </div>
      <div class="col-md-4">
        <div class="metric-card">
          <span class="metric-label">Classification</span>
          <strong>${escapeHtml(analysis.classification)}</strong>
        </div>
      </div>
      <div class="col-md-4">
        <div class="metric-card">
          <span class="metric-label">Token Count</span>
          <strong>${analysis.tokens.length}</strong>
        </div>
      </div>
    </div>
    <h3 class="h6">Tokens</h3>
    <div class="token-area mb-3">
      ${analysis.tokens.map((token) => `<span class="token-chip">${escapeHtml(token)}</span>`).join("")}
    </div>
    <h3 class="h6">Keywords</h3>
    <div class="token-area mb-3">
      ${
        analysis.keywords.length
          ? analysis.keywords.map((keyword) => `<span class="token-chip keyword-chip">${escapeHtml(keyword)}</span>`).join("")
          : '<span class="text-muted">None detected.</span>'
      }
    </div>
    <h3 class="h6">Suggested Chatbot Response</h3>
    <p class="response-preview">${escapeHtml(analysis.response)}</p>
    <h3 class="h6">NLP Tools</h3>
    <p class="mb-0">${escapeHtml((analysis.nlp_tools || []).join(" + "))}</p>
  `;
}

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

voiceBtn.addEventListener("click", startVoiceInput);

speakLastBtn.addEventListener("click", () => {
  if (!lastBotResponse) {
    alert("Send a message first so there is a bot response to speak.");
    return;
  }
  speakText(lastBotResponse);
});

clearHistoryBtn.addEventListener("click", async () => {
  if (!confirm("Clear all saved conversation history?")) return;
  await fetch("api/history.php", { method: "DELETE" });
  chatBox.innerHTML = "";
  await loadHistory();
});

refreshHistoryBtn.addEventListener("click", loadHistory);

analyzeBtn.addEventListener("click", async () => {
  const message = analyzerInput.value.trim();
  if (!message) {
    analyzerOutput.innerHTML = '<p class="text-danger mb-0">Please enter text to analyze.</p>';
    return;
  }

  const response = await fetch("api/analyze.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ message }),
  });
  const data = await response.json();

  if (!response.ok) {
    analyzerOutput.innerHTML = `<p class="text-danger mb-0">${escapeHtml(data.error)}</p>`;
    return;
  }

  renderAnalyzerOutput(data.analysis);
});

loadHistory();
