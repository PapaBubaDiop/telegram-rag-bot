# 🤖 Telegram RAG Bot

A production-ready Telegram chatbot powered by **OpenAI**, **Retrieval-Augmented Generation (RAG)** and **PHP**.

The project demonstrates a complete AI backend integrating Telegram Bot API, OpenAI embeddings, MySQL vector storage and semantic search to generate context-aware responses.

---

## Features

- 🤖 Telegram Bot API integration
- 🧠 Retrieval-Augmented Generation (RAG)
- 📚 Knowledge Base Embeddings
- 💬 OpenAI Chat Completion
- 🔍 Semantic Search
- ☁️ PHP Backend
- 🗄 MySQL Database
- ⚡ Webhook Processing
- 📈 Conversation History
- 🔐 External Configuration (credentials are not stored in the repository)

---

## Architecture

```
                  Telegram
                      │
                Telegram Bot
                      │
                  Webhook PHP
                      │
          ┌───────────┴───────────┐
          │                       │
      OpenAI API             MySQL Database
          │                       │
     Embeddings             Knowledge Base
          │                       │
          └────────── Semantic Search ────────┐
                                              │
                                   Context Builder
                                              │
                                       AI Response
                                              │
                                          Telegram
```

---

## Tech Stack

- PHP 8
- MySQL
- OpenAI API
- Telegram Bot API
- REST API
- JSON
- Embeddings
- Retrieval-Augmented Generation (RAG)

---

## Repository Structure

```
telegram-rag-bot
│
├── telegram/
│   ├── bot.php
│   ├── comtrade_bot.php
│   ├── comtrade_cv.php
│   ├── comtrade_test.php
│   └── papabot.php
│
├── sql/
│   ├── schema.sql
│   ├── embed_cv.sql
│   └── bot_history.sql
│
├── README.md
└── .gitignore
```

---

## Database

The repository contains SQL schema for the core RAG components.

Main tables:

| Table | Description |
|--------|-------------|
| **embed_cv** | Stores vector embeddings for the knowledge base used by semantic search. |
| **bot_history** | Stores user conversations and interaction history. |

---

## Security

Sensitive information is intentionally excluded from the repository.

Configuration (Telegram tokens, OpenAI API keys and database credentials) is stored outside the project.

---

## Use Cases

- AI Assistants
- Enterprise Knowledge Bases
- Corporate Documentation Search
- FAQ Bots
- Internal Support Systems
- Semantic Search
- RAG Applications

---

## Author

**Vadim Bashurov**

Senior Software Engineer

- Swift / UIKit
- PHP
- MySQL
- Telegram Bot API
- OpenAI API
- Artificial Intelligence
- RAG
- Machine Learning

---

## License

Published for portfolio and educational purposes.
