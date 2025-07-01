pipeline {
    agent any

    environment {
        SONARQUBE_SCANNER = 'SonarQubeScanner'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --prefer-dist --no-ansi --no-interaction --no-scripts'
                sh 'npm install'
                sh 'npm run dev'
            }
        }

        stage('SonarQube Scan') {
            steps {
                withSonarQubeEnv('SonarQubeScanner') {
                    sh 'sonar-scanner'
                }
            }
        }
    }

    post {
        success {
            echo "Development branch pipeline completed."
        }
    }
}
