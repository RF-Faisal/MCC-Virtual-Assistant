------------------------------------
Top 10 Teams
------------------------------------
SELECT
RANK() OVER(ORDER BY TEAM_RATING DESC) "Position",
TEAM_NAME "Team",
TEAM_RATING "Rating"
FROM TEAM
WHERE ROWNUM <= 10;



------------------------------------
Total Reward Point of All Members
------------------------------------
SELECT
SUM(REWARD_POINT) "Total Reward Point of All Members"
FROM MEMBER;



------------------------------------
Individual Ranking
------------------------------------
SELECT
RANK() OVER(ORDER BY RATING DESC) "Position",
USER_PROFILE.USERNAME "Username",
NAME "Name",
TEAM_NAME "Team",
RATING "MCC Rating"
FROM MEMBER, USER_PROFILE
WHERE USER_PROFILE.USERNAME=MEMBER.USERNAME;



------------------------------------
List of Alumni from CSE-16
------------------------------------
SELECT
NAME "Name",
STUDENT_ID "Student ID",
BATCH "Batch"
FROM ALUMNI, USER_PROFILE
WHERE USER_PROFILE.USERNAME=ALUMNI.USERNAME
AND BATCH='CSE-16';



------------------------------------
Rank by CodeForces Rating
------------------------------------
SELECT
RANK() OVER(ORDER BY RATING DESC) "Position",
NAME "Name",
HANDLE "Handle",
RATING "CodeForces Rating"
FROM ONLINE_JUDGE, USER_PROFILE
WHERE USER_PROFILE.USERNAME=ONLINE_JUDGE.USERNAME
AND JUDGE='CodeForces';



------------------------------------
Rank by CodeChef Solve
------------------------------------
SELECT
RANK() OVER(ORDER BY SOLVE_COUNT DESC) "Position",
NAME "Name",
HANDLE "Handle",
SOLVE_COUNT "Total Solve"
FROM ONLINE_JUDGE, USER_PROFILE
WHERE USER_PROFILE.USERNAME=ONLINE_JUDGE.USERNAME
AND JUDGE='CodeChef';



------------------------------------
Badges Achieved by Members
------------------------------------
SELECT
NAME "Name",
BADGE_NAME "Badge"
FROM USER_PROFILE, ACHIEVED_BY, BADGE
WHERE USER_PROFILE.USERNAME=ACHIEVED_BY.USERNAME
AND ACHIEVED_BY.BADGE_ID=BADGE.BADGE_ID;



------------------------------------
Badge List with by No of Achievement
------------------------------------
SELECT
NAME "Name",
COUNT(DISTINCT BADGE_NAME) "Total Badges"
FROM USER_PROFILE, ACHIEVED_BY, BADGE
WHERE USER_PROFILE.USERNAME=ACHIEVED_BY.USERNAME
AND ACHIEVED_BY.BADGE_ID=BADGE.BADGE_ID
GROUP BY NAME;



------------------------------------
T-Shirt Size having more than or equal 6 Badges
------------------------------------
SELECT
NAME "Name",
TSHIRT_SIZE "T-Shirt Size",
"Total Badges"
FROM USER_PROFILE, (
  SELECT
  USER_PROFILE.USERNAME,
  COUNT(DISTINCT BADGE_NAME) "Total Badges"
  FROM USER_PROFILE, ACHIEVED_BY, BADGE
  WHERE USER_PROFILE.USERNAME=ACHIEVED_BY.USERNAME
  AND ACHIEVED_BY.BADGE_ID=BADGE.BADGE_ID
  GROUP BY USER_PROFILE.USERNAME
) SUBQUERY
WHERE USER_PROFILE.USERNAME=SUBQUERY.USERNAME
AND SUBQUERY."Total Badges">=6;



------------------------------------
T-Shirt Size having more than or equal 6 Badges
------------------------------------
SELECT
NAME "Name",
TSHIRT_SIZE "T-Shirt Size",
COUNT(DISTINCT BADGE_NAME) "Total Badges"
FROM USER_PROFILE, ACHIEVED_BY, BADGE
WHERE USER_PROFILE.USERNAME=ACHIEVED_BY.USERNAME
AND ACHIEVED_BY.BADGE_ID=BADGE.BADGE_ID
GROUP BY NAME, TSHIRT_SIZE
HAVING COUNT(DISTINCT BADGE_NAME)>=6;



------------------------------------
Available Items to Order at Reward Store
------------------------------------
SELECT
REWARD_NAME "Items",
AVG(REQUIRED_POINTS) "Required Points"
FROM REWARD
WHERE ORDER_STATUS='Available'
GROUP BY REWARD_NAME;



------------------------------------
Pending Orders at Reward Store
------------------------------------
SELECT
NAME "Name",
REWARD_NAME "Items"
FROM USER_PROFILE, REWARD
WHERE USER_PROFILE.USERNAME=REWARD.USERNAME
AND ORDER_STATUS='Processing';



------------------------------------
Designation of Alumnis and Job Duration
------------------------------------
SELECT
NAME "Name",
BATCH "Batch",
ORGANIZATION "Organization",
DESIGNATION "Designation",
TO_CHAR(FLOOR(MONTHS_BETWEEN(SYSDATE,START_DATE)/12))||' Y '||TO_CHAR(FLOOR(MOD(MONTHS_BETWEEN(SYSDATE,START_DATE),12)))||' M' "Duration"
FROM USER_PROFILE, ALUMNI, PROFESSION
WHERE USER_PROFILE.USERNAME=ALUMNI.USERNAME
AND ALUMNI.USERNAME=PROFESSION.USERNAME
AND END_DATE IS NULL;



------------------------------------
Project List of Alumnis from CSE-18
------------------------------------
SELECT
NAME "Name",
PROJECTS "Project Name"
FROM USER_PROFILE,ALUMNI,ALUMNI_PROJECT
WHERE USER_PROFILE.USERNAME=ALUMNI.USERNAME
AND ALUMNI.USERNAME=ALUMNI_PROJECT.USERNAME
AND BATCH='CSE-18';



------------------------------------
Course List
------------------------------------
SELECT
OVERVIEW "Course Name",
PRICE||'.00' "Price",
DURATION||' Days' "Duration",
TO_CHAR(START_TIME,'DD Mon YYYY') "Start Date",
TO_CHAR(START_TIME+DURATION,'DD Mon YYYY') "Expected End Date"
FROM COURSE
ORDER BY START_TIME;



------------------------------------
Total Earned Amount from Each Course
------------------------------------
SELECT
COURSE_ID "Course_ID",
SUM(AMOUNT) "Total Earned"
FROM ENROLL
GROUP BY COURSE_ID;



------------------------------------
Task Assigner and Performer
------------------------------------
SELECT
ASSIGNER.NAME "Assigned by",
PERFORMER.NAME "Performer",
TASK.TASK_ID "Task",
DATE_OF_ASSIGN "Date of Assign",
DATE_OF_ASSIGN+DURATION "Deadline",
FLOOR(DATE_OF_ASSIGN+DURATION-SYSDATE) "Days Left"
FROM TASK, PERFORM, USER_PROFILE ASSIGNER, USER_PROFILE PERFORMER
WHERE TASK.TASK_ID=PERFORM.TASK_ID
AND ASSIGNER.USERNAME=TASK.USERNAME
AND PERFORMER.USERNAME=PERFORM.USERNAME
