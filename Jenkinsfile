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
                sh "docker run --rm --tty --volume ${env.WORKSPACE}:/app composer:1.9.3 install --ignore-platform-reqs --no-interaction"
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
}