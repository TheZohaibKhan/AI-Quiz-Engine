<div align="center">
# 🧠 AI Quiz Engine

### AI-powered examination, assessment, and personalized learning platform

**Create exams · Generate questions with AI · Evaluate answers ·
Discover learning gaps**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?logo=livewire&logoColor=white)](https://livewire.laravel.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-pgvector-4169E1?logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![AI](https://img.shields.io/badge/AI-Multi--provider-7C3AED)](#-ai-capabilities)
[![Deployment](https://img.shields.io/badge/Deployment-Render-46E3B7?logo=render&logoColor=black)](https://render.com/)

**[🚀 Open Live Demo](https://ai-quiz-engine-b049.onrender.com)** ·
**[📦 GitHub
Repository](https://github.com/TheZohaibKhan/AI-Quiz-Engine)**
</div>

------------------------------------------------------------------------

## 📌 Overview

**AI Quiz Engine** is a full-stack quiz and examination platform built
with **Laravel 12**, **Livewire 4**, and **PostgreSQL with pgvector**.
It provides separate workflows for teachers and students, bringing exam
creation, question-bank management, AI-assisted question generation,
answer evaluation, contextual hints, result analysis, and semantic
similarity search into one application.

Teachers can create and publish exams, reuse questions, generate
questions with AI, import questions from CSV, review results, export
reports, and monitor AI usage. Students can discover published exams,
complete timed attempts, mark questions to review later, submit answers,
view results and leaderboards, and receive AI-generated learning hints
where enabled.

The application is designed around clear role permissions, server-side
exam timing, usage controls for AI calls, and automated tests.

## 🌐 Live Demo & Source Code

  -------------------------------------------------------------------------------------
  Resource                            Link
  ----------------------------------- -------------------------------------------------
  **Live application**                https://ai-quiz-engine-b049.onrender.com

  **GitHub repository**               https://github.com/TheZohaibKhan/AI-Quiz-Engine
  -------------------------------------------------------------------------------------

> **Deployment note:** The live application is hosted on Render.
> Availability may depend on the hosting service and its current
> deployment configuration. AI-powered features also depend on valid
> provider configuration and available usage limits.

------------------------------------------------------------------------

## ✨ Main Features

### 👨‍🏫 Teacher workspace

-   Create, edit, publish, unpublish, and delete examinations.
-   Add, edit, delete, and reorder questions.
-   Preview an unpublished exam as a student would see it.
-   Generate questions using AI, with streaming generation and
    generation history.
-   Reuse questions from a searchable question bank.
-   Import questions in bulk using CSV with row validation.
-   Review student submissions and exam-level results.
-   View live submission counters through Laravel Reverb.
-   Export student result cards and class results as PDF.
-   Monitor AI request usage and costs in the AI Usage dashboard.

### 👨‍🎓 Student workspace

-   View a student dashboard and available published exams.
-   Take timed examinations with a server-enforced countdown.
-   Save questions for review later during an attempt.
-   Submit answers and view scored results.
-   Review previous attempts.
-   View leaderboards for exams where leaderboards are enabled.
-   Receive Socratic-style AI hints for short-answer questions where
    enabled.
-   Get practice-question recommendations based on results and related
    content.

### 🔐 Accounts and access control

-   Login and registration.
-   Email verification and password-reset workflows.
-   Two-factor authentication support through the authentication stack.
-   Separate teacher and student routes.
-   Role-aware dashboards and route authorization.
-   Teachers manage their own exams; students access published exams
    through the student workflow.

### 📄 Import, export, and real-time features

-   CSV question import with validation and handling for malformed rows.
-   PDF result cards and teacher class-result exports.
-   Queued PDF export processing and signed download links.
-   Live submission updates through Laravel Reverb broadcasting.
-   Live leaderboard updates and rank display.

------------------------------------------------------------------------

## 🧭 How the Platform Works

### 1. End-to-end examination workflow

``` mermaid
flowchart TD
    A([Start]) --> B[Teacher signs in]
    B --> C[Create examination]
    C --> D{Generate questions with AI?}
    D -- Yes --> E[Choose topic, type, and difficulty]
    E --> F[AI generates structured questions]
    F --> G[Teacher reviews and edits questions]
    D -- No --> H[Add questions manually or from question bank]
    G --> I[Configure duration and exam settings]
    H --> I
    I --> J[Publish examination]
    J --> K[Exam appears to eligible students]
    K --> L[Student starts timed attempt]
    L --> M[Answer questions and mark items for review]
    M --> N[Submit attempt or timer expires]
    N --> O[Server validates and scores answers]
    O --> P[Save attempt and calculate result]
    P --> Q[Student reviews result]
    Q --> R[Recommendations and leaderboard, if enabled]
    R --> S([End])
```

### 2. Teacher workflow

``` mermaid
flowchart TD
    A[Teacher Dashboard] --> B[Examinations]
    B --> C{Choose action}
    C --> D[Create or edit exam]
    C --> E[Open question bank]
    C --> F[Generate questions with AI]
    D --> G[Add, edit, reorder questions]
    E --> G
    F --> H[Review generated questions]
    H --> G
    G --> I[Preview exam]
    I --> J{Ready to publish?}
    J -- No --> G
    J -- Yes --> K[Publish exam]
    K --> L[Monitor submissions]
    L --> M[Review results]
    M --> N[Export PDF / analyze class performance]
    L --> O[Check AI usage dashboard]
```

### 3. Student workflow

``` mermaid
flowchart TD
    A[Student signs in] --> B[Student Dashboard]
    B --> C[Browse published exams]
    C --> D[Open exam instructions]
    D --> E[Start attempt]
    E --> F[Answer questions]
    F --> G{Need to revisit a question?}
    G -- Yes --> H[Mark for review]
    H --> F
    G -- No --> I{Submit or timer expires}
    F --> I
    I --> J[Server validates submission]
    J --> K[Automatic scoring and AI grading where applicable]
    K --> L[Save result]
    L --> M[Review answers and feedback]
    M --> N[Related practice recommendations]
    M --> O[Leaderboard, if enabled]
```

### 4. AI question-generation workflow

``` mermaid
flowchart TD
    A[Teacher requests question generation] --> B[Select topic, question type, and difficulty]
    B --> C[Check application limits and provider configuration]
    C --> D[QuestionGeneratorAgent]
    D --> E{AI response valid?}
    E -- Yes --> F[Parse structured question output]
    F --> G[Display generated questions]
    G --> H[Teacher reviews, edits, and selects]
    H --> I[Save selected questions to exam]
    E -- No --> J[Handle error or retry/fallback path]
    J --> K[Show feedback to teacher]
```

### 5. Answer evaluation workflow

``` mermaid
flowchart TD
    A[Student submits answers] --> B[Validate attempt and answers]
    B --> C{Question type}
    C -- Objective / exact-match --> D[Automatic rule-based scoring]
    C -- Short answer --> E[AutoGraderAgent]
    E --> F[Retrieve semantically related context when available]
    F --> G[Evaluate response and generate feedback]
    G --> H{AI grading available?}
    H -- Yes --> I[Use returned score and explanation]
    H -- No --> J[Graceful exact-match fallback where applicable]
    D --> K[Calculate total score]
    I --> K
    J --> K
    K --> L[Persist attempt result]
    L --> M[Show result and recommendations]
```

> These diagrams describe the intended application workflow based on the
> project implementation and development plan. Exact paths and available
> actions can depend on account role, exam settings, and AI-provider
> configuration.

------------------------------------------------------------------------

## 🤖 AI Capabilities

The project uses Laravel's AI SDK and dedicated agent classes for
different tasks.

  -----------------------------------------------------------------------
  Component                           Responsibility
  ----------------------------------- -----------------------------------
  `QuestionGeneratorAgent`            Generates structured questions
                                      based on topic, question type, and
                                      difficulty. Supports
                                      synchronous/queued paths and
                                      streaming generation workflows.

  `AutoGraderAgent`                   Assists with grading short-answer
                                      responses and returning evaluation
                                      details such as explanations or
                                      suggestions.

  `HintAgent`                         Produces Socratic-style hints to
                                      guide a student's reasoning instead
                                      of simply giving away the answer.

  `QuestionSimilarityService`         Uses vector similarity search to
                                      find semantically related questions
                                      and learning context.

  Embedding jobs and observer         Generate or refresh embeddings
                                      asynchronously as question/attempt
                                      data changes.
  -----------------------------------------------------------------------

### AI provider configuration

The project is configured for multiple provider integrations. The
environment variables in `.env.example` include:

-   Anthropic
-   Google Gemini
-   OpenAI
-   Ollama for local development
-   Additional provider integrations defined in `config/ai.php`,
    including Azure, Cohere, DeepSeek, Groq, Mistral, OpenRouter, and
    others

The configured default text provider is Anthropic; provider availability
and fallback behavior depend on the application configuration and
credentials.

### AI-assisted question generation

Teachers request questions with a topic, type, and difficulty. The AI
agent returns structured output, which the teacher can inspect before
saving. This human-review step helps teachers check relevance,
correctness, and suitability before publication.

### AI-assisted grading

Short-answer responses can be evaluated using the AI grader. The
application can provide scoring feedback, explanations, and suggestions.
A graceful exact-match fallback is described in the project plan for
applicable cases when AI grading is unavailable.

### Socratic hints

The hint workflow is intended to help students reason through a question
by offering guidance in stages rather than immediately revealing a final
answer. Streaming responses are supported through Livewire's streaming
workflow.

------------------------------------------------------------------------

## 🔎 Semantic Search & Retrieval-Augmented Grading

The application uses **PostgreSQL with pgvector** to store and query
vector embeddings.

### How similarity search works

``` mermaid
flowchart TD
    A[Question or attempt content changes] --> B[Observer / queued embedding job]
    B --> C[Generate text embedding]
    C --> D[Store vector in PostgreSQL with pgvector]
    E[New grading or recommendation request] --> F[Embed query content]
    F --> G[QuestionSimilarityService]
    G --> H[Vector similarity query]
    D --> H
    H --> I[Retrieve semantically related questions or context]
    I --> J[Use context for grading calibration or practice recommendations]
```

### Why use vector search?

Traditional keyword search primarily matches exact words.
Embedding-based similarity can find related content even when the
wording differs. In this project, similarity search supports grading
calibration and recommendations for practice or review.

Implementation notes from the project include:

-   1536-dimensional embedding columns on questions and attempts.
-   HNSW-indexed vector columns.
-   A `QuestionObserver` and queued jobs for generating embeddings.
-   A backfill command for existing question embeddings.
-   A similarity-search tool used by the AI grader for relevant context.

Actual search quality depends on the selected embedding model, stored
data, and database configuration.

------------------------------------------------------------------------

## 💸 AI Usage, Rate Limits & Cost Controls

AI requests can incur provider charges. The application includes usage
logging and configurable controls to reduce unexpected usage.

### Included controls

-   Per-user AI request rate limiting.
-   AI usage records, including token and cost information where
    available.
-   Configurable daily budget.
-   Embedding usage tracking.
-   Teacher-facing AI Usage dashboard.
-   A budget hard-stop mechanism described in the project plan.

Example values from `.env.example`:

``` env
AI_RATE_LIMIT_PER_MINUTE=30
AI_DAILY_BUDGET=5.00
```

These are example configuration values, not a promise of free AI usage.
Confirm the provider's current pricing, configure suitable limits, and
monitor usage in the deployment environment.

------------------------------------------------------------------------

## 🏗️ Architecture

``` mermaid
flowchart TB
    U[Teacher / Student Browser] --> UI[Livewire 4 UI]
    UI --> R[Laravel 12 Routes and Middleware]
    R --> AUTH[Authentication and Role Authorization]
    AUTH --> APP[Exam, Question, Attempt and Result Logic]
    APP --> DB[(PostgreSQL)]
    APP --> AI[Laravel AI SDK Agents]
    AI --> P[Configured AI Provider]
    APP --> V[(pgvector Similarity Search)]
    APP --> Q[Laravel Queue Jobs]
    Q --> EMB[Embedding Generation / CSV Import / PDF Export]
    EMB --> DB
    APP --> RT[Laravel Reverb Events]
    RT --> UI
    APP --> PDF[PDF Result Exports]
    PDF --> DL[Signed Download URL]
    APP --> USAGE[AI Usage Tracking and Budget Controls]
    USAGE --> DB
```

### Main technology stack

  -----------------------------------------------------------------------
  Layer                               Technology
  ----------------------------------- -----------------------------------
  Backend framework                   Laravel 12

  Reactive UI                         Livewire 4

  UI components                       Flux UI

  Styling and frontend build          Tailwind CSS 4, Vite

  Database                            PostgreSQL

  Semantic vector search              pgvector

  AI integration                      Laravel AI SDK and provider agents

  Authentication                      Laravel Fortify

  Real-time broadcasting              Laravel Reverb

  Background processing               Laravel queue system

  PDF export                          `barryvdh/laravel-dompdf` and
                                      queued export jobs

  Testing                             Pest 4 and Laravel test utilities

  Containerization                    Docker

  Hosting                             Render

  Automation                          GitHub Actions CI workflow;
                                      deployment workflow configuration
                                      is in the repository
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 🗃️ Core Data Model

The main domain models include `User`, `Exam`, `Question`, `Attempt`,
and `AiUsage`.

``` mermaid
erDiagram
    USER ||--o{ EXAM : creates
    EXAM ||--o{ QUESTION : contains
    USER ||--o{ ATTEMPT : submits
    EXAM ||--o{ ATTEMPT : receives
    USER ||--o{ AI_USAGE : generates
    QUESTION }o..o{ QUESTION : semantic_similarity

    USER {
        bigint id
        string role
        string name
        string email
    }
    EXAM {
        bigint id
        bigint user_id
        string title
        boolean published
        integer duration
    }
    QUESTION {
        bigint id
        bigint exam_id
        string type
        text content
        vector embedding
    }
    ATTEMPT {
        bigint id
        bigint user_id
        bigint exam_id
        json answers
        vector embedding
    }
    AI_USAGE {
        bigint id
        bigint user_id
        string provider
        integer tokens
        decimal cost
    }
```

> The diagram is a conceptual overview of key relationships, not a
> complete schema specification. Consult the migrations for exact column
> names, constraints, and relationship definitions.

------------------------------------------------------------------------

## 🛡️ Security & Reliability

Project implementation and development notes include:

-   Role-based route groups for teachers and students.
-   Authorization around teacher-owned exams and published student
    exams.
-   Server-enforced exam countdown and an idempotency guard for
    submission.
-   Signed PDF download URLs with a limited expiry.
-   AI rate limits and daily budget controls.
-   Automated tests using fake AI responses so CI does not need real
    provider API calls.
-   Dependency automation and CI workflows in `.github/workflows/`.

For production deployment, use `APP_ENV=production`, set
`APP_DEBUG=false`, force HTTPS, keep secrets in deployment environment
variables, rotate database credentials, and monitor failed jobs and
application logs.

------------------------------------------------------------------------

## 🧪 Testing

The repository uses **Pest 4** with Laravel testing utilities. AI tests
use fake agent responses to avoid making live API calls during automated
test runs.

Typical commands:

``` bash
php artisan test
```

Run a focused test file:

``` bash
php artisan test tests/Feature/ExampleTest.php
```

Use the actual test paths present in the repository when running focused
tests. The project development plan records **183 tests and 91.7%
coverage** at the time that plan was written; this is a historical
project note and may differ from the current branch or test run.

------------------------------------------------------------------------

## ⚙️ Run Locally

### Prerequisites

-   PHP 8.3 or newer (the repository's Dockerfile uses PHP 8.4)
-   Composer 2
-   Node.js 22 and npm
-   PostgreSQL with the `pgvector` extension enabled
-   Git
-   An API key for any external AI provider whose features you want to
    use

Docker-based setup is also supported by the repository.

### 1. Clone the repository

``` bash
git clone https://github.com/TheZohaibKhan/AI-Quiz-Engine.git
cd AI-Quiz-Engine
```

### 2. Configure environment variables

``` bash
cp .env.example .env
```

On Windows Command Prompt, use:

``` bat
copy .env.example .env
```

Edit `.env` to set the correct database host, database name, username,
password, application URL, and any AI-provider API key you plan to use.
Do not commit the real `.env` file.

The example configuration uses PostgreSQL and includes placeholders for
provider credentials. If you use Docker/Sail, follow the repository's
container configuration and service names.

### 3. Install dependencies

``` bash
composer install
npm install
```

### 4. Generate the application key

``` bash
php artisan key:generate
```

### 5. Prepare PostgreSQL

Create the database and ensure the `pgvector` extension is available and
enabled according to the repository migrations and your PostgreSQL
setup.

Then run:

``` bash
php artisan migrate
```

### 6. Build frontend assets

For development:

``` bash
npm run dev
```

For a production asset build:

``` bash
npm run build
```

### 7. Start Laravel

``` bash
php artisan serve
```

Open the local URL shown by Artisan, usually `http://127.0.0.1:8000`.

> Real-time broadcasting, queue workers, mail delivery, and AI features
> may need additional environment configuration and background
> processes. The exact setup depends on which features you are testing.

------------------------------------------------------------------------

## 🐳 Docker / Deployment

The repository includes a `Dockerfile` and Docker-related configuration
for a containerized Laravel deployment. The Dockerfile builds frontend
assets and configures PHP-FPM, Nginx, and Supervisor for the Render
service port.

The live application link is:

**https://ai-quiz-engine-b049.onrender.com**

### Production checklist

-   Set a strong `APP_KEY`.
-   Set `APP_ENV=production` and `APP_DEBUG=false`.
-   Configure a managed PostgreSQL database with pgvector support.
-   Store AI provider keys in environment variables, not source control.
-   Use secure HTTPS cookies and HTTPS-only access.
-   Configure persistent storage if the deployment needs uploaded files.
-   Configure queue workers and failed-job monitoring for queued AI,
    embedding, and export jobs.
-   Review database indexes and query performance.
-   Confirm `/up` health checks and monitor service availability.
-   Audit dependencies and rotate any credentials that may have been
    exposed.

The repository's `PLAN.md` identifies production queue/cache hardening,
security headers, observability, and final deployment automation as
follow-up work; check the current branch and deployment configuration
before assuming every item is complete.

------------------------------------------------------------------------

## 🗂️ Repository Structure

A simplified view of important paths:

``` text
AI-Quiz-Engine/
├── app/
│   ├── Ai/Agents/
│   │   ├── AutoGraderAgent.php
│   │   ├── HintAgent.php
│   │   └── QuestionGeneratorAgent.php
│   ├── Console/Commands/
│   │   └── BackfillQuestionEmbeddingsCommand.php
│   ├── Events/
│   │   └── AttemptSubmittedEvent.php
│   ├── Jobs/
│   │   ├── ExportExamResultsJob.php
│   │   ├── GenerateAttemptEmbeddingJob.php
│   │   ├── GenerateQuestionEmbeddingJob.php
│   │   ├── ImportQuestionsFromCsvJob.php
│   │   └── ProcessGeneratedQuestionsJob.php
│   ├── Models/
│   │   ├── AiUsage.php
│   │   ├── Attempt.php
│   │   ├── Exam.php
│   │   └── Question.php
│   ├── Observers/
│   │   └── QuestionObserver.php
│   └── Services/
│       └── QuestionSimilarityService.php
├── config/
│   └── ai.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
├── routes/
│   ├── web.php
│   └── settings.php
├── tests/
├── docker/
├── .github/workflows/
├── Dockerfile
├── PLAN.md
├── composer.json
├── package.json
└── .env.example
```

------------------------------------------------------------------------

## 🛣️ Development Roadmap

The project plan documents these completed development phases:

-   **Phase 1 --- Foundation:** authentication, role-based routing,
    Livewire starter setup, settings, and CI.
-   **Phase 2 --- Core exam engine:** exam/question CRUD, student
    attempts, automatic scoring, results, and authorization.
-   **Phase 3 --- AI integration:** question generation, answer grading,
    Socratic hints, and provider integration.
-   **Phase 4 --- Workflow enhancements:** timer, review-later flags,
    streaming generation, Reverb events, PDF exports, question bank, CSV
    import, and exam preview.
-   **Phase 5 --- Advanced AI:** pgvector embeddings, similarity search,
    RAG-enhanced grading, recommendations, and AI cost controls.
-   **Phase 6 --- Production hardening:** queue/cache reliability,
    security hardening, performance review, observability, and
    deployment improvements. Check `PLAN.md` for the current task
    status.

------------------------------------------------------------------------

## 🤝 Contributing

Contributions, issue reports, and suggestions are welcome.

1.  Fork the repository.
2.  Create a feature branch.
3.  Make a focused change and add or update tests.
4.  Run the relevant test suite and frontend build.
5.  Open a pull request describing the change.

For changes involving AI providers, use fake responses in automated
tests and avoid making paid API calls from CI.

## 🔐 Environment & Secret Management

-   Never commit `.env`, API keys, database passwords, private
    certificates, or production credentials.
-   Keep `.env.example` limited to placeholders and safe defaults.
-   Rotate any credential that was accidentally committed or exposed.
-   Treat AI output as untrusted input and validate generated content
    before storing or displaying it.
-   Apply least-privilege access to database users and deployment
    tokens.

## 📚 Useful Documentation

| Resource | Documentation |
|---|---|
| Laravel 12 | [Official Documentation](https://laravel.com/docs/12.x) |
| Laravel AI SDK | [Official Documentation](https://laravel.com/docs/12.x/ai) |
| Livewire | [Official Website](https://livewire.laravel.com) |
| Flux UI | [Official Website](https://fluxui.dev) |
| PostgreSQL | [Official Documentation](https://www.postgresql.org/docs/) |
| pgvector | [GitHub Repository](https://github.com/pgvector/pgvector) |
| Laravel Reverb | [Official Website](https://reverb.laravel.com) |
| Pest | [Official Website](https://pestphp.com) |

## 👨‍💻 Project Links

-   **Developer / repository owner:**
    [TheZohaibKhan](https://github.com/TheZohaibKhan)
-   **GitHub repository:**
    https://github.com/TheZohaibKhan/AI-Quiz-Engine
-   **Live demo:** https://ai-quiz-engine-b049.onrender.com

<div align="center">
**Built with Laravel, Livewire, PostgreSQL, and AI-assisted learning
workflows.**
</div>
