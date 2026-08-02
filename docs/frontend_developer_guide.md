# Frontend & Mobile Developer Guide
## N8N Automation & AI Assessment System Integration

This document details the user flows, screens, and **API documentation** for integrating the AI Assessment System into the existing Flutter (Mobile) and Next.js (Web) frontends.

> **Note on N8N Settings:** The N8N automation settings (Webhooks, AI models, Scheduling) will be handled directly in the **existing Laravel Admin Dashboard**. Frontend developers do not need to build screens for these settings.

---

## 1. 📱 Flutter App (Researcher Flow)

The researcher (formerly "student") will view tasks, submit files, and see feedback within the Flutter app.

### 📍 Screen 1: My Tasks List
- **Purpose:** Display all assigned tasks to the researcher.
- **UI Elements:** A list/grid showing Task Title, Deadline, and Status (Pending, Submitted, Rejected, Approved). Highlight overdue or rejected tasks.
- **API Endpoint:** `GET /api/researcher/tasks`
- **Headers:** `Authorization: Bearer {token}`
- **Response Expected:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "task_id": 10,
        "title": "Data Analysis Assignment",
        "deadline": "2026-07-20 23:59:00",
        "status": "pending",
        "is_notified": true
      }
    ]
  }
  ```

### 📍 Screen 2: Task Details & Submission
- **Purpose:** View full task details and upload the completed file.
- **UI Elements:** Text description, embedded tutorial video player, file picker button (restrict to `.docx`, `.xlsx`, or text input), Submit button.
- **API Endpoint:** `POST /api/researcher/submissions`
- **Headers:** `Authorization: Bearer {token}`, `Content-Type: multipart/form-data`
- **Request Body:**
  - `task_assignment_id` (integer)
  - `file` (File - `.docx`, `.xlsx`) or `content_text` (String)
- **Logic:** Once submitted, change status to "Submitted" (disabling the submit button).

### 📍 Screen 3: AI Feedback View
- **Purpose:** View the final feedback and score after mentor approval.
- **UI Elements:** Final Overall Score, breakdown of scores per rubric criteria, Mentor comments, and AI's flagged issues/edited excerpt.
- **API Endpoint:** `GET /api/researcher/submissions/{submission_id}/feedback`
- **Headers:** `Authorization: Bearer {token}`
- **Response Expected:**
  ```json
  {
    "overall_score": 85,
    "mentor_comments": "Great work, but check the formatting.",
    "rubric_scores": [
      {"name": "Accuracy", "score": 25, "max": 30}
    ],
    "flagged_issues": ["Missing conclusion paragraph"]
  }
  ```

---

## 2. 💻 Next.js Web (Mentor Dashboard)

Mentors manage tasks, create rubrics, and review AI feedback before approving it.

### 📍 Screen 1: Mentor Rubric Management
- **Purpose:** Create and manage custom rubrics.
- **UI Elements:** List of rubrics. Form to add/edit a rubric (Title, Description, dynamic input for criteria: Name & Max Points).
- **API Endpoint:** `GET | POST | PUT | DELETE /api/mentor/rubrics`
- **POST Request Body:**
  ```json
  {
    "title": "Standard Essay Rubric",
    "description": "Used for weekly essays",
    "criteria": [
      {"name": "Grammar", "points": 20},
      {"name": "Content", "points": 80}
    ]
  }
  ```

### 📍 Screen 2: Task Management & Creation
- **Purpose:** Assign a task to a group of researchers.
- **UI Elements:** List of tasks. "Create Task" modal.
- **Fields:** Title, Description, Deadline, Video URL, Rubric Dropdown (lists their rubrics + global rubrics).
- **API Endpoint:** `GET | POST /api/mentor/tasks`
- **POST Request Body:**
  ```json
  {
    "title": "Week 1 Essay",
    "description": "Write a 500 word essay.",
    "rubric_id": 5,
    "deadline": "2026-07-20 23:59:00",
    "video_url": "https://youtube.com/..."
  }
  ```

### 📍 Screen 3: Submissions Inbox (Pending Reviews)
- **Purpose:** See which researchers have submitted work.
- **UI Elements:** Data table showing Researcher Name, Task Name, Submitted Date, Status.
- **API Endpoint:** `GET /api/mentor/submissions`

### 📍 Screen 4: AI Review & Approval Screen
- **Purpose:** Review AI feedback, adjust scores, and approve/reject.
- **UI Elements:** Split screen. Left: Researcher's file. Right: AI Report. Action area to override scores/comments and Approve/Reject buttons.
- **API Endpoints:** 
  - **View:** `GET /api/mentor/submissions/{submission_id}/feedback`
  - **Approve:** `PATCH /api/mentor/feedback/{feedback_id}/approve`
    - Request Body: `{"mentor_score_override": 90, "mentor_comments_override": "Excellent"}`
  - **Reject:** `PATCH /api/mentor/feedback/{feedback_id}/reject`
    - Request Body: `{"rejection_note": "Please fix section 2 and resubmit"}`

### 📍 Screen 5: Group Analytics
- **Purpose:** View performance metrics for the mentor's group.
- **UI Elements:** Charts showing average scores and completion rates.
- **API Endpoint:** `GET /api/mentor/analytics/group/{group_id}`

---

## 3. 👑 Next.js Web (Coordinator Dashboard)

> **⚠️ IMPORTANT FOR FRONTEND DEV:** 
> The Coordinator acts as the Admin for the Next.js app. Some of these screens might be missing from the old design. You must integrate these into the existing Coordinator sidebar.

### 📍 Screen 1: Global Overview
- **Purpose:** High-level system statistics.
- **UI Elements:** Summary cards (Total Researchers, Mentors, Active Tasks, System-wide Avg Score, Completion Rate).
- **API Endpoint:** `GET /api/coordinator/dashboard/overview`

### 📍 Screen 2: System Rubrics (Global)
- **Purpose:** Create rubrics that any mentor can use.
- **UI Elements:** Same UI as Mentor Rubric Management.
- **API Endpoint:** `GET | POST | PUT | DELETE /api/coordinator/rubrics`

### 📍 Screen 3: Global Tasks Monitor
- **Purpose:** Monitor all tasks across all mentors.
- **UI Elements:** Data table showing all tasks. Must have filtering (by Mentor, Status, Date).
- **API Endpoint:** `GET /api/coordinator/tasks`

### 📍 Screen 4: Analytics & Export
- **Purpose:** Deep dive into system-wide performance.
- **UI Elements:** Detailed charts filterable by date/group. "Export to Excel/PDF" button.
- **API Endpoints:** 
  - **View:** `GET /api/coordinator/analytics/group/{group_id}`
  - **Export:** `GET /api/coordinator/analytics/export`

---
*Note: Notifications for tasks and feedback approvals/rejections will be delivered seamlessly through the existing chat infrastructure.*
