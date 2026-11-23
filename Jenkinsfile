
pipeline {
    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
        buildDiscarder(logRotator(numToKeepStr: '15'))
        timeout(time: 30, unit: 'MINUTES')
    }

    triggers {
        githubPush()
        cron('H 1 * * *')
    }

    environment {
        // GANTI dengan username Docker Hub kelompok
        IMAGE = 'devinaaa/antrisehat-app'

        IMAGE_TAG = "${env.BUILD_NUMBER}"
        HEALTH_URL = 'http://localhost:8000/health'
    }

    stages {

        // STAGE 1: CHECKOUT
        stage('Checkout') {
            steps {
                checkout scm
                sh 'git log -1 --oneline'
            }
        }

        // STAGE 2: TEST
        stage('Test') {
            steps {
                sh 'docker build --target test -t $IMAGE:test .'
            }
        }

        // STAGE 3: BUILD IMAGE
        stage('Build Image') {
            steps {
                sh '''
                    docker build --target production \
                        -t "$IMAGE:$IMAGE_TAG" \
                        -t "$IMAGE:latest" .
                '''
            }
        }

        // STAGE 4: PUSH IMAGE
        stage('Push Image') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub-credentials',
                        usernameVariable: 'DH_USER',
                        passwordVariable: 'DH_PASS'
                    )
                ]) {
                    sh '''
                        echo "$DH_PASS" | docker login \
                            -u "$DH_USER" --password-stdin

                        docker push "$IMAGE:$IMAGE_TAG"
                        docker push "$IMAGE:latest"
                    '''
                }
            }

            post {
                always {
                    sh 'docker logout || true'
                }
            }
        }

        // STAGE 5: DEPLOY
        stage('Deploy') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub-credentials',
                        usernameVariable: 'DH_USER',
                        passwordVariable: 'DH_PASS'
                    ),
                    file(
                        credentialsId: 'antrisehat-env',
                        variable: 'ENV_FILE'
                    )
                ]) {
                    sh '''
                        # Siapkan environment
                        cp "$ENV_FILE" .env

                        export DOCKERHUB_USER="$DH_USER"
                        export IMAGE_TAG="$BUILD_NUMBER"

                        # Ambil image dari Docker Hub
                        docker compose pull app scheduler

                        # Jalankan database dan tunggu healthy
                        docker compose up -d --wait db

                        # Jalankan migrasi database
                        docker compose run --rm --no-deps app \
                            php artisan migrate --force

                        # Jalankan aplikasi dan scheduler
                        docker compose up -d --remove-orphans

                        # Periksa container
                        docker compose ps
                    '''
                }
            }
        }

        // STAGE 6: SMOKE TEST
        stage('Smoke Test') {
            steps {
                sh '''
                    for i in $(seq 1 15); do

                        code=$(curl -s -o /dev/null \
                            -w "%{http_code}" "$HEALTH_URL" || true)

                        if [ "$code" = "200" ]; then
                            echo "Health OK"
                            echo "Smoke Test BERHASIL"
                            exit 0
                        fi

                        echo "Menunggu aplikasi ($i/15), HTTP $code"
                        sleep 4
                    done

                    echo "Smoke Test GAGAL"
                    docker compose logs --tail=100 app
                    exit 1
                '''
            }
        }
    }

    // POST ACTION
    post {
        always {
            sh 'rm -f .env || true'
            sh 'docker logout || true'
        }

        success {
            echo """
                =================================
                PIPELINE ANTRISEHAT BERHASIL!
                Build Number : ${env.BUILD_NUMBER}
                Image        : ${env.IMAGE}
                Tag          : ${env.IMAGE_TAG}
                =================================
            """
        }

        failure {
            echo """
                =================================
                PIPELINE ANTRISEHAT GAGAL!
                Periksa Console Output.
                =================================
            """
        }
    }
}
