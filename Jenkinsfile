node {
def CONTAINER_NAME="php nginx"
def CONTAINER_TAG="development"
def DOCKER_HUB_USER="f1banac1"
def HTTP_PORT="8090"

stage('Initialize'){
        def dockerHome = tool 'myDocker'
        def mavenHome  = tool 'myMaven'
        env.PATH = "${dockerHome}/bin:${mavenHome}/bin:${env.PATH}"
    }

stage('Checkout') {
        checkout scm
    }

stage('Build'){
        sh "mvn clean install"
    }

stage('Sonar'){
    try {
        sh "mvn sonar:sonar"
    } catch(error){
        echo "The sonar server could not be reached ${error}"
    }
 }

stage("Image Prune"){
        imagePrune(CONTAINER_NAME)
    }

stage ('Build Infrastructure images') {
	sh 'echo “Run docker-cumpose”'
	sh 'cd docker/DEV && docker-compose up --build'
}

stage ('Composer install'){
	sh 'docker exec php composer --no-progress --prefer-dist install'
}

stage ('intit project') {
	sh 'echo “Tests will back”'
	sh 'docker exec php ./init --env=Development --overwrite=y'
}

stage ('migrate DB') {
	sh 'docker exec php ./yii migrate  --interactive=0'
}

stage ('UNIT TEST') {
	sh 'echo “unit test comming soon”'
	}

stage ('Deploy'){
	sh 'echo "Deployment"'
}
}

def imagePrune(containerName){
    try {
        sh "docker image prune -f"
        sh "docker stop $containerName"
    } catch(error){}
}

def pushToImage(containerName, tag){
    sh "docker tag $containerName:$tag docker-registry.travelinsides.com/$containerName:$tag"
    sh "docker push docker-registry.travelinsides.com/$containerName:$tag"
    echo "Image push complete"
}

def runApp(containerName, tag, dockerHubUser, httpPort){
    sh "docker pull docker-registry.travelinsides.com/$containerName"
    sh "docker run -d --rm -p $httpPort:$httpPort --name $containerName docker-registry.travelinsides.com/$containerName:$tag"
    echo "Application started on port: ${httpPort} (http)"
}