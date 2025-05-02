<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
</head>
<body>
    <h1>Create Job Post</h1>

    <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="title">Job Title:</label>
        <input type="text" name="title" id="title" required><br>

        <label for="description">Job Description:</label>
        <textarea name="description" id="description" required></textarea><br>

        <label for="location">Location:</label>
        <input type="text" name="location" id="location" required><br>

        <label for="type">Job Type:</label>
        <input type="text" name="type" id="type" required><br>

        <label for="salary_min">Min Salary:</label>
        <input type="number" name="salary_min" id="salary_min"><br>

        <label for="salary_max">Max Salary:</label>
        <input type="number" name="salary_max" id="salary_max"><br>

        <label for="deadline">Deadline:</label>
        <input type="date" name="deadline" id="deadline" required><br>

        <label for="category">Category:</label>
        <input type="text" name="category" id="category" required><br>

        <label for="employer_id">Employer:</label>
        <input type="number" name="employer_id" id="employer_id" required><br>

        <label for="company_logo">Company Logo (Optional):</label>
        <input type="file" name="company_logo" id="company_logo"><br>

        <button type="submit">Post Job</button>
    </form>

</body>
</html>