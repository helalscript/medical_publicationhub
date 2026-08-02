# PROJECT PLANNING DOCUMENT
## N8N Automation + AI Assessment System
> Educational Platform — Task Delivery, AI Feedback & Mentor Review

---

| Field | Details |
|-------|---------|
| **Client** | Ibraheem Altamimi |
| **Project** | Educational App (Publication Hub) — N8N Automation System |
| **Stack** | Laravel (Backend) + Flutter (Mobile) + Next.js (Web) + N8N + AI Agent |
| **Date** | 7 July 2026 |
| **Prepared By** | Development Team |
| **Version** | v1.0 — Planning Draft |

---

## 1. PROJECT OVERVIEW

This document outlines the complete planning and workflow design for integrating an N8N Automation Agent into the existing educational platform. The system will automate task delivery to students, collect submissions, assess work using AI, and allow mentors and coordinators to review and approve feedback before sending it to students.

### 1.1 Goals & Objectives

- Automate task delivery to students via in-app chat (no manual WhatsApp messages)
- Allow students to submit work (Word/Excel) through the app
- Use AI Agent to assess student submissions against rubric criteria
- Provide Mentors a dashboard to review, edit, and approve AI feedback
- Give Coordinators a high-level dashboard to monitor all students and progress
- Save all records to database for analytics and reporting

### 1.2 System Components

| Component | Technology | Role |
|-----------|------------|------|
| Backend API | Laravel (PHP) | Core data, file storage, API endpoints, webhooks |
| Mobile App | Flutter | Student interface — chat, task, submission, feedback |
| Web Dashboard | Next.js | Mentor & Coordinator dashboard |
| Automation Engine | N8N | Workflow automation — triggers, scheduling, AI calls |
| AI Assessment | Claude / GPT API | Evaluate student work, generate score & feedback |
| Database | MySQL | Store tasks, submissions, feedback, analytics |
| File Storage | Laravel Storage | Store uploaded Word/Excel files from students |

---

## 2. USER ROLES & PERMISSIONS

### 👨‍🎓 STUDENT

- View assigned tasks in chat and dashboard
- Watch tutorial video embedded in task
- Submit work as `.docx` or `.xlsx` file upload, or plain text
- Receive feedback and score in chat after mentor approval (also via email)
- View past submission history and scores
- Receive push notification when new task is assigned or feedback arrives

### 👨‍🏫 MENTOR

- Create tasks with title, description, rubric, deadline, and video or Google Drive link
- Trigger task delivery manually or set on schedule
- View all student submissions for their group
- Review AI-generated feedback and score for each student
- Edit, adjust, or add comments to the AI feedback before sending
- Approve feedback — triggers N8N to send feedback to student chat
- Reject submission and request resubmission with a note
- View analytics for their student group — scores, completion rate

### 🏢 COORDINATOR / ADMIN

- Full visibility across all mentors and all student groups
- View all tasks, submissions, AI reports, and mentor feedback
- Monitor overall completion rates, average scores, late submissions
- Export reports as PDF or Excel
- Manage user accounts — add/remove students and mentors
- Configure N8N schedule and automation settings
- Override or escalate any submission if needed

---

## 3. N8N AUTOMATION — COMPLETE WORKFLOW

N8N acts as the central automation brain. It connects to Laravel via REST API and Webhooks. Below is the complete step-by-step workflow.

---

### FLOW 1 — TASK DELIVERY WORKFLOW

| Step | Action | Who | How (Technical) |
|------|--------|-----|-----------------|
| 1 | Trigger fires | N8N | Schedule trigger (e.g. every Monday 9AM) OR manual trigger by mentor OR webhook from Laravel when mentor creates a task |
| 2 | Fetch task data | N8N → Laravel | `GET /api/tasks/{id}` — returns title, description, rubric, deadline, video URL |
| 3 | Fetch student list | N8N → Laravel | `GET /api/groups/{id}/students` — returns all students in the group |
| 4 | Loop per student | N8N | N8N loops through each student and sends personalized message |
| 5 | Send chat message | N8N → Laravel | `POST /api/chat/send` — sends task details + video link to student chat |
| 6 | Send push notification | N8N → FCM | Firebase push notification to Flutter app |
| 7 | Create task record | N8N → Laravel | `POST /api/task-assignments` — marks task as sent, records timestamp |
| 8 | Update dashboard | Auto | Next.js dashboard auto-shows new task card from Laravel DB |

---

### FLOW 2 — STUDENT SUBMISSION WORKFLOW

| Step | Action | Who | How (Technical) |
|------|--------|-----|-----------------|
| 1 | Student uploads file | Student | Flutter/Next.js file picker — selects `.docx` or `.xlsx` or types text |
| 2 | File saved to server | Laravel | `POST /api/submissions` — Laravel saves file to storage, records in DB with student ID, task ID, timestamp |
| 3 | Webhook fires to N8N | Laravel → N8N | Laravel sends POST to N8N webhook URL — payload: `submission_id`, `student_id`, `task_id`, `file_path` |
| 4 | N8N starts AI flow | N8N | N8N receives webhook, starts AI Assessment workflow automatically |
| 5 | Deadline check | N8N | N8N checks if submission is on time or late — records status |

---

### FLOW 3 — AI ASSESSMENT WORKFLOW

| Step | Action | Who | How (Technical) |
|------|--------|-----|-----------------|
| 1 | Download file | N8N | N8N fetches file from Laravel storage using `file_path` from webhook |
| 2 | Extract content | N8N | N8N reads `.docx` or `.xlsx` content — extracts plain text for AI |
| 3 | Fetch rubric | N8N → Laravel | `GET /api/tasks/{id}/rubric` — gets scoring criteria for this task |
| 4 | Send to AI Agent | N8N → AI API | N8N calls Claude/GPT API with: student content + rubric criteria + evaluation instructions |
| 5 | AI generates report | AI Agent | AI returns: overall score, score per rubric item, feedback comments, flagged issues, edited excerpt |
| 6 | Save AI report | N8N → Laravel | `POST /api/ai-feedback` — N8N saves AI report to Laravel DB linked to submission ID |
| 7 | Notify mentor | N8N → Laravel | `POST /api/chat/send` (mentor channel) OR push notification — "New submission ready for review" |

---

### FLOW 4 — MENTOR REVIEW & FEEDBACK WORKFLOW

| Step | Action | Who | How (Technical) |
|------|--------|-----|-----------------|
| 1 | Mentor opens dashboard | Mentor | Next.js dashboard — sees list of submissions pending review |
| 2 | Mentor views AI report | Mentor | `GET /api/submissions/{id}/feedback` — sees AI score, comments, flagged issues |
| 3 | Mentor edits feedback | Mentor | Mentor can adjust score, add personal comments, flag for resubmission |
| 4 | Mentor approves | Mentor → Laravel | `PATCH /api/feedback/{id}/approve` — marks feedback as approved |
| 5 | Webhook fires to N8N | Laravel → N8N | Laravel triggers N8N webhook: `feedback_approved`, payload includes feedback content |
| 6 | N8N sends feedback | N8N → Laravel | `POST /api/chat/send` — sends score + full feedback to student chat |
| 7 | Push notification | N8N → FCM | Student receives push notification "Your feedback is ready" |
| 8 | Record saved | Auto | Laravel records final feedback with timestamp, mentor ID, score |

---

### FLOW 5 — RESUBMISSION WORKFLOW

| Step | Action | Who | How (Technical) |
|------|--------|-----|-----------------|
| 1 | Mentor rejects | Mentor → Laravel | `PATCH /api/feedback/{id}/reject` — mentor adds rejection reason and resubmission note |
| 2 | N8N triggered | Laravel → N8N | Webhook fires with rejection details |
| 3 | Student notified | N8N → Chat | N8N sends message to student: rejection reason + what to fix + new deadline |
| 4 | Student resubmits | Student | Student uploads new file — same submission flow repeats (Flow 2 → 3 → 4) |
| 5 | Record updated | Laravel | Submission record updated — resubmission count tracked |

---

## 4. LARAVEL API ENDPOINTS — REQUIRED

The following API endpoints must be built in Laravel so that N8N can connect and automate the workflow.

| Method | Endpoint | Purpose | Called By |
|--------|----------|---------|-----------|
| `GET` | `/api/tasks/{id}` | Get task details, rubric, video URL | N8N |
| `GET` | `/api/groups/{id}/students` | Get student list for a group | N8N |
| `GET` | `/api/videos/{id}` | Get tutorial video URL | N8N |
| `POST` | `/api/chat/send` | Send message to student or mentor chat | N8N |
| `POST` | `/api/task-assignments` | Record that task was sent to student | N8N |
| `POST` | `/api/submissions` | Student submits file upload | Flutter/Next.js |
| `GET` | `/api/submissions/{id}` | Get submission details + file path | N8N / Mentor |
| `POST` | `/api/ai-feedback` | Save AI assessment report | N8N |
| `GET` | `/api/submissions/{id}/feedback` | Get AI feedback for mentor review | Mentor Dashboard |
| `PATCH` | `/api/feedback/{id}/approve` | Mentor approves feedback | Mentor Dashboard |
| `PATCH` | `/api/feedback/{id}/reject` | Mentor rejects, adds note | Mentor Dashboard |
| `GET` | `/api/tasks/{id}/rubric` | Get rubric scoring criteria | N8N |
| `GET` | `/api/analytics/group/{id}` | Get group analytics — scores, completion | Coordinator Dashboard |
| `GET` | `/api/analytics/export` | Export analytics as PDF/Excel | Coordinator |

---

## 5. MENTOR DASHBOARD — FEATURES

The Mentor Dashboard will be built in Next.js. It gives mentors full control over task management, submission review, and feedback approval.

### 5.1 Dashboard Screens

| Screen | What the Mentor Sees / Does |
|--------|-----------------------------|
| 📋 Task List | List of all tasks created — title, deadline, how many students submitted |
| ➕ Create Task | Form to create new task — title, description, rubric items, deadline, video URL, group assignment |
| 📥 Submissions Inbox | List of student submissions pending AI review and mentor review — sortable by date, student name, status |
| 🤖 AI Report View | Full AI feedback for one submission — score breakdown per rubric item, comments, flagged issues, edited excerpt |
| ✏️ Edit Feedback | Mentor can edit AI score, add personal comment, mark specific rubric items |
| ✅ Approve / ❌ Reject | One-click approve (sends feedback to student) or reject with a typed reason |
| 📊 Group Analytics | Charts — completion rate, average score, late submission count, per-student score history |
| 📨 Message Student | Mentor can send a direct message to a student from dashboard |

### 5.2 Submission Status Flow (Mentor View)

```
Pending Submission   →  Student hasn't submitted yet
Submitted            →  File received, waiting for AI assessment
AI Processing        →  N8N + AI currently evaluating
Pending Mentor Review →  AI done, waiting for mentor
Approved             →  Mentor approved, feedback sent to student
Rejected             →  Mentor rejected, student must resubmit
Resubmitted          →  Student submitted again after rejection
```

---

## 6. COORDINATOR DASHBOARD — FEATURES

The Coordinator Dashboard gives administrators a bird's-eye view of all mentors, student groups, tasks, and overall system performance.

| Screen | What the Coordinator Sees / Does |
|--------|----------------------------------|
| 🏠 Overview | Total students, total mentors, tasks this week, overall completion rate, average AI score |
| 👥 All Groups | List of all student groups — each group's mentor, student count, active task, completion % |
| 📋 All Tasks | All tasks across all mentors — filter by group, date, status |
| 📊 Analytics | Detailed charts — per student, per group, per task. Score trends over time. |
| 📤 Export Reports | Export full analytics to Excel or PDF — per group or system-wide |
| 👤 User Management | Add/remove students and mentors. Assign students to groups. Assign mentors to groups. |
| ⚙️ N8N Settings | Configure automation — schedule timing, AI model selection, webhook URLs |
| 🔔 Alerts | Flag late submissions, students with low scores, groups with low completion |

### 6.1 Key Analytics Metrics

| Metric | Description |
|--------|-------------|
| Completion Rate | % of students who submitted on time per task |
| Average Score | Mean AI/mentor score per task and per student |
| Late Submissions | Count and % of submissions after deadline |
| Resubmission Rate | How often students resubmit after rejection |
| Mentor Response Time | How fast mentors review and approve AI feedback |
| Student Progress | Score trend over multiple tasks for each student |

---

## 7. AI ASSESSMENT — HOW IT WORKS

The AI Agent (Claude or GPT) will evaluate student submissions based on the rubric defined for each task.

### 7.1 AI Input (What we send to AI)

- Student's full submission text (extracted from `.docx` or `.xlsx`)
- Task title and full description / brief
- Rubric — list of criteria with max score per item (e.g. Accuracy: 30pts, Clarity: 20pts, Format: 10pts)
- Task deadline and whether submission was on time
- Language preference (Arabic, English, or Both)

### 7.2 AI Output (What AI returns)

- Overall Score (e.g. 78 / 100)
- Score per rubric item (e.g. Accuracy: 25/30, Clarity: 18/20, Format: 8/10)
- Written feedback comment per rubric item
- General overall feedback summary
- Flagged issues (e.g. missing section, grammar problems, incorrect data)
- Edited excerpt — AI rewrites a key part of the submission to show the student how to improve

### 7.3 AI Prompt Structure (N8N)

Inside N8N, the AI call will be structured with a system prompt like:

```
System: You are an expert educational assessor. Evaluate the student's work based on the rubric provided.

Task: {task_title} — {task_description}

Rubric: {rubric_items}

Student Submission: {student_content}

Return JSON with: overall_score, rubric_scores[], feedback[], flagged_issues[], edited_excerpt
```

---

## 8. IMPLEMENTATION PHASES

| Phase | Deliverable | Timeline | Team |
|-------|-------------|----------|------|
| Phase 1 | Laravel API — all required endpoints, DB schema, webhook setup | Week 1–2 | Backend Dev |
| Phase 2 | N8N workflow setup — all 5 flows configured and tested | Week 2–3 | N8N / Automation Dev |
| Phase 3 | Flutter — student submission UI, feedback view, notifications | Week 2–3 | Flutter Dev |
| Phase 4 | Next.js — Mentor Dashboard (all screens listed in Section 5) | Week 3–4 | Frontend Dev |
| Phase 5 | Next.js — Coordinator Dashboard (all screens listed in Section 6) | Week 4–5 | Frontend Dev |
| Phase 6 | AI Agent integration — prompt tuning, rubric-based scoring tested | Week 3–4 | AI / N8N Dev |
| Phase 7 | Full system integration testing — end-to-end flow with real data | Week 5 | All Team |
| Phase 8 | Arabic RTL integration across all interfaces | Week 5–6 | Frontend Dev |
| Phase 9 | UAT (User Acceptance Testing) with client + fixes | Week 6 | All Team |
| Phase 10 | Deployment to production server | Week 6 | Backend / DevOps |

---

## 9. OPEN QUESTIONS — CLIENT CONFIRMATION NEEDED

The following items need to be confirmed by the client before development begins:

| # | Question | Why It Matters |
|---|----------|----------------|
| 1 | Does the existing Laravel backend have a Chat API, or do we need to build it? | N8N connects to chat via this API |
| 2 | Where are tutorial videos hosted? (YouTube / Vimeo / own server) | Determines how N8N fetches and embeds video links |
| 3 | What language should AI feedback be in? (Arabic / English / Both) | Affects AI prompt design and output format |
| 4 | Should AI feedback go directly to student, or always pass through mentor first? | Determines if mentor review is mandatory or optional |
| 5 | Can students resubmit? How many times? | Need to design resubmission flow and limits |
| 6 | Do you have a server for hosting N8N, or should we set up cloud hosting? | N8N needs its own server / cloud instance |
| 7 | Is there a fixed rubric for all tasks, or different rubric per task? | Affects how we store and send rubric to AI |
| 8 | Should coordinator have ability to override mentor feedback? | Affects permission design in dashboard |
| 9 | Is push notification required on Flutter, or chat message is enough? | Determines Firebase FCM setup need |
| 10 | Do you need Arabic RTL on the Flutter mobile app as well, or only Next.js? | Affects Flutter localization effort |

---

*— End of Document —*

*Prepared for client review. All details subject to change based on client feedback.*
