CRM: Deployment Pipeline
-------------------------------------------

This pipeline deploys CRM application

### Prerequisite ###
    Git repository key 'git-crm'
    AWS Programmatic access keys: "aws-${ENV}"
    SSH rsa key: "ssh-${ENV}",
    Vault file: "vault-${ENV}",
    Slack token: "slack-${PROJECT}-deploys

### Stages

1. Checkout - checkout SCM

2. Build - Download vendor libs. Create an application tarball. Push to S3

3. Deploy - Pull an artifcat from S3 and deploy it to a server

4. Post - Notify a developer in Slack. Clean up workspace


### Jenkins jobs
We have created 3 Jenkins jobs, each of which is responsible for its own environment, which can be judged by the name:
    deploy-crm-dev
    deploy-crm-stage
    deploy-crm-prod

While all three jobs use this pipeline underneath, we configure them individually to track build trends for each environment separately.


### Triggers
Deployment to Dev and Prod environment is triggered by the Bitbucket webhook. As soon as a new commit appears in the develop or master branch, Jenkins will automatically invoke the corresponding job.

Deployment to Stage has to be done manually because of a workflow. Once a new release branch is ready for testing, the teamlead notifies QA team that they can start testing a specific release. The jobs is configured with a handy branch filter that sorts all releases in descending order. Thus, the latest release is always on top.

Please note that deploy-crm-gtt is automatically triggered after successful deployment in prod by deploy-crm-prod.
