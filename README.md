# 🎓 AI Quiz Engine

> An AI-powered quiz and examination platform built with Laravel 12, Livewire 4, PostgreSQL, pgvector, Gemini AI, and Laravel Reverb.

AI Quiz Engine is a full-stack web application designed to simplify the creation, management, delivery, evaluation, and analysis of online examinations.

The platform provides dedicated workflows for **Teachers** and **Students**, with AI-assisted question generation, answer evaluation, contextual hints, similarity search, AI usage tracking, examination management, leaderboards, and result analysis.

---

## 🌐 Live Demo

🚀 **Live Application**

https://ai-quiz-engine-b049.onrender.com

💻 **GitHub Repository**

https://github.com/TheZohaibKhan/AI-Quiz-Engine

---

## ✨ Features

### 👨‍🏫 Teacher Features

- Create and manage examinations
- Edit existing examinations
- Add, edit, and manage questions
- Maintain a question bank
- Generate questions using AI
- Import questions using CSV
- Publish examinations
- View student submissions
- Review examination results
- Monitor AI usage
- Export examination and result information as PDF
- Manage AI-assisted examination workflows

### 👨‍🎓 Student Features

- Student dashboard
- View available examinations
- Take online examinations
- Submit examination attempts
- View examination results
- Review previous attempts
- View exam leaderboards
- Receive AI-powered assistance where enabled

---

# 🤖 AI Capabilities

AI Quiz Engine integrates AI into multiple stages of the examination workflow.

## AI Question Generation

Teachers can generate examination questions automatically based on the configured examination topic or content.

The application supports configurable AI providers and models through environment variables and application configuration.

## AI Answer Evaluation

The platform supports AI-assisted evaluation of short-answer responses, including partial-credit grading where applicable.

## Socratic Hints

Students can receive AI-generated hints designed to guide their reasoning rather than directly revealing the answer.

## Similarity Search

The application uses embeddings and vector similarity search to retrieve semantically related questions and information.

## Multiple AI Providers

The application is designed to work with multiple AI providers, including:

- Google Gemini
- Anthropic
- OpenAI
- Ollama

AI provider configuration is handled through environment variables and application configuration.

---

# 🧠 AI Usage & Cost Controls

The application includes AI usage tracking and rate limiting.

Features include:

- AI request tracking
- AI usage logging
- Per-minute AI rate limiting
- Daily AI budget configuration
- Embedding usage tracking
- AI provider/model configuration
- AI usage monitoring

These controls help manage AI consumption and reduce unexpected API costs.

Example configuration:

```env
AI_RATE_LIMIT_PER_MINUTE=30
AI_DAILY_BUDGET=5.00
