pipeline {
    agent any
    options {
        withAWS(region:'us-east-1',credentials:'hybrid_jenkins_ansible_s3_key')
        disableConcurrentBuilds()
    }
    stages {
        stage('Initialize'){
            steps{
                echo "Initialization"
            }
        }
        stage('Checkout'){
            steps {
                checkout scm
            }
        }
        stage('Yii Composer'){
            steps{
                sh "composer install --ignore-platform-reqs --no-interaction"
                echo "${env.WORKSPACE}"
            }
        }
        stage('Upload Vendor'){
            steps{
                script {
                    String branchName = env.BRANCH_NAME.replace("/","_")
                    sh "tar -zcf ${branchName}.tar.gz vendor/ composer.lock composer.json"
                    s3Upload(file:"${branchName}.tar.gz", bucket:'hybrid-project-releases', path:"crm/${branchName}.tar.gz");
                    sh "rm -f ${branchName}.tar.gz"
                }
            }
        }
    }

    post {
        always {
            wrap([$class: 'BuildUser']) {
                mattermostSend(
                    color: 'good',
                    icon: "https://jenkins.io/images/logos/jenkins/jenkins.png",
                    message: ":package: **CRM Sales**: <${env.BUILD_URL}console|${env.JOB_NAME}> #${env.BUILD_NUMBER} (*${currentBuild.currentResult}*) \nBranch: `${env.BRANCH_NAME}` \nRun by: ${env.BUILD_USER}",
                    channel: "deploys",
                    endpoint: "https://chat.travel-dev.com/hooks/wfkq819uzjnx8ne3htjnw9pimr"
                )

                mattermostSend(
                    color: '#2A42EE',
                    icon: "https://jenkins.io/images/logos/jenkins/jenkins.png",
                    message: ":package: **CRM Sales**: <${env.BUILD_URL}console|${env.JOB_NAME}> #${env.BUILD_NUMBER} (*${currentBuild.currentResult}*) \nBranch: `${env.BRANCH_NAME}` \nRun by: ${env.BUILD_USER}",
                    channel: "crm-developers",
                    endpoint: "https://chat.travel-dev.com/hooks/sbchqikahidcuxjetpqjt3cp8h"
                )
            }
        }
    }
}