# Chintan-Trivia

Chintan-Trivia is a web-based quiz application that allows users to create, manage, and participate in quizzes. The project's aim is to generate AI-driven quizzes and transform how students and teachers interact in the classroom, enabling teachers to easily generate quizzes with the help of our AI assistant.

## Table of Contents
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features
- **User Authentication**: Users can sign up, log in, and log out.
- **Group Management**: Create, join, and delete groups for collaborative quizzes.
- **Quiz Creation**: Users can create and manage their own quizzes.
- **AI Quiz Generation**: Automatically generate quizzes using AI.
- **Quiz Participation**: Users can take quizzes and view their results.
- **Leaderboard**: Displays top performers based on quiz scores.

## Technology Stack
- **Backend**: PHP
- **Database**: SQL
- **Frontend**: HTML, CSS, JavaScript, TailwindCSS

## Installation

To set up the project locally using XAMPP, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Arpit1005/Chintan-Trivia.git
   cd Chintan-Trivia
   ```
2. **Set up XAMPP**:
   - Start the XAMPP control panel.
   - Start the Apache and MySQL modules.

3. **Set up the database**:
   - Open phpMyAdmin by navigating to `http://localhost/phpmyadmin`.
   - Create a new database for the project.
   - Run SQL commands given in the Structure.sql file

4. **Configure the application**:
   - Update the database connection settings in the configuration file (e.g., `Connect.php`).

5. **Add your Gemini API key**:
   - Open `QuizCreate.php`.
   - Locate the section where the API key is required and add your Gemini API key:
     ```js
     const apiKey = 'YOUR_GEMINI_API_KEY_HERE';
     ```

6. **Start the application**:
   - Place the project folder in the `htdocs` directory of your XAMPP installation (e.g., `C:\xampp\htdocs\Chintan-Trivia`).
   - Open your web browser and navigate to `http://localhost/Chintan-Trivia`.

## Usage

- Create an account or log in to start using the application.
- Explore the features, create quizzes, and participate in them!

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request if you have suggestions or improvements.

## License

This project is licensed under the MIT License.

---

Feel free to modify any sections to fit your project specifics better!
