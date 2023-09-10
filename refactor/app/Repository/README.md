# Readme

## Overview

This readme file provides an overview of the code quality assessment for the **JobRepository**. It highlights the strengths, areas that need improvement, and identifies issues that should be addressed. I have tried to refactor whatever was possible to refactor. There could be more improvements but I don't have access to the rest of the code and I did not want to break the code. 

## Table of Contents
1. [What is Good](#what-is-good)
2. [What can be Improved](#what-can-be-improved)
3. [What is Bad](#what-is-bad)
4. [Changes I have made](#changes-i-have-made)

---

## What is Good

1. The code uses the repository pattern, which is good for decoupling the logic.

2. Laravel events are used.

3. Dependency injection is used.

---

## What can be Improved

1. Function names could be improved to be self-explanatory.

2. Variable names could be improved to be self-explanatory.

3. Too many conditional statements could be reduced to fewer.

4. Response status could be set to `false` or `true` as **boolean** rather than **string**.

5. Some logic could be moved to helper functions. 

---

## What is Bad

1. The function lengths could be reduced into smaller, more related blocks of code.

2. The use of custom helpers could help in the separation of concerns.

3. A repository should only deal with the database; all the validation logic should either be implemented in the controller or service.

4. The notification-related logic should be implemented in a separate class.

5. Email-related logic should be handled in a separate class.

6. We can also use Laravel Queue/Jobs to send emails/notifications so the user doesn't have to wait for a response while the email/notification is sending.

7. I think accessing environment variables directly is a bad practice.

---

## Changes I have made

1. Moved the **distanceFeed** function from **JobController** to **JobRepository**

2. Breaked the **sendNotificationTranslators** into different parts and renamed it to **sendNotificationToTranslators**

3. While there is already `$model` set in the parent constructor still there is the use of static `Job::` in the repository which is not a good practice, I changed it to `$this->model`. 

4. Breaked the **sendSMSNotificationToTranslator** into parts.
