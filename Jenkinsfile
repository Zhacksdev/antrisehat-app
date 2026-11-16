// AntriSehat — Jenkins CI/CD pipeline (PRD: 6 stage hijau)
//   Checkout -> Test -> Build -> Push -> Deploy (+ migrasi) -> Smoke Test
//
// Jenkins Credentials yang dibutuhkan:
//   - dockerhub-credentials : Username with password (akun Docker Hub)
//   - antrisehat-env        : Secret file berisi .env production
//
// Agent wajib memiliki Docker Engine + Docker Compose v2 dan akses ke port 8000.

pipeline {
    agent any

    triggers {
        githubPush()          // webhook GitHub (via Ngrok)
        cron('H 1 * * *')     // build terjadwal tiap malam (tantangan K05)
    }

    options {
        timestamps()
        disableConcurrentBuilds()
        buildDiscarder(logRotator(numToKeepStr: '15'))
        timeout(time: 30, unit: 'MINUTES')
    }

    environment {
        IMAGE_NAME          = 'antrisehat-app'
        IMAGE_TAG           = "${env.BUILD_NUMBER}"
        COMPOSE_PROJECT_NAME = 'antrisehat'
        HEALTH_URL          = 'http://localhost:8000/health'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                sh 'git log -1 --oneline'
            }
        }

        stage('Test') {
            steps {
                // Stage `test` pada Dockerfile menjalankan `php artisan test`;
                // build gagal bila ada test yang gagal.
                sh 'docker build --target test -t ${IMAGE_NAME}:test .'
            }
        }

        stage('Build') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials',
                                                  usernameVariable: 'DOCKERHUB_USER',
                                                  passwordVariable: 'DOCKERHUB_PASS')]) {
                    sh '''
                        docker build --target production \
                          -t "$DOCKERHUB_USER/$IMAGE_NAME:$IMAGE_TAG" \
                          -t "$DOCKERHUB_USER/$IMAGE_NAME:latest" .
                    '''
                }
            }
        }

        stage('Push') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials',
                                                  usernameVariable: 'DOCKERHUB_USER',
                                                  passwordVariable: 'DOCKERHUB_PASS')]) {
                    sh '''
                        echo "$DOCKERHUB_PASS" | docker login -u "$DOCKERHUB_USER" --password-stdin
                        docker push "$DOCKERHUB_USER/$IMAGE_NAME:$IMAGE_TAG"
                        docker push "$DOCKERHUB_USER/$IMAGE_NAME:latest"
                    '''
                }
            }
            post {
                always { sh 'docker logout || true' }
            }
        }

        stage('Deploy') {
            steps {
                withCredentials([
                    usernamePassword(credentialsId: 'dockerhub-credentials',
                                     usernameVariable: 'DOCKERHUB_USER',
                                     passwordVariable: 'DOCKERHUB_PASS'),
                    file(credentialsId: 'antrisehat-env', variable: 'ENV_FILE')
                ]) {
                    sh '''
                        cp "$ENV_FILE" .env
                        export DOCKERHUB_USER IMAGE_TAG

                        # 1) Database dulu, tunggu sampai healthy
                        docker compose up -d --wait db

                        # 2) Migrasi sebagai langkah terpisah (PRD 2.6) agar kegagalan terlihat di log
                        docker compose run --rm --no-deps app php artisan migrate --force
                        docker compose run --rm --no-deps app php artisan db:seed --force

                        # 3) Jalankan / perbarui app + scheduler dengan image build ini
                        docker compose up -d --no-build --remove-orphans app scheduler
                        docker compose ps
                    '''
                }
            }
        }

        stage('Smoke Test') {
            steps {
                sh '''
                    for i in $(seq 1 20); do
                        if curl -fsS "$HEALTH_URL"; then
                            echo ""
                            echo "Smoke test OK"
                            exit 0
                        fi
                        echo "Menunggu aplikasi siap... ($i/20)"
                        sleep 3
                    done
                    echo "Smoke test GAGAL: $HEALTH_URL tidak mengembalikan 200"
                    docker compose logs --tail=100 app
                    exit 1
                '''
            }
        }
    }

    post {
        always {
            sh 'rm -f .env || true'
            sh 'docker image prune -f || true'
        }
        success {
            echo "Deploy ${IMAGE_NAME}:${IMAGE_TAG} berhasil."
        }
        failure {
            echo 'Pipeline gagal — periksa console log stage terkait.'
        }
    }
}
