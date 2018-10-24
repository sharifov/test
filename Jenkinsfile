node {

stage ('Build Infrastructure') {
	sh 'echo “Run docker-cumpose”'
	sh 'cd docker/DEV'
	sh 'docker-compose up'
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