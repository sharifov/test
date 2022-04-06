CRM: Terraform
-------------------------------------------

![](../../docs/imgs/infrastructure.png)


### Prerequisite ###

* AWS Account with admin privileges
* Route53 Hosted Zone
* Linux environment to run the code
* Terraform v1.1.6
* SOPS v3.7.2

### Infrastructure deployment

##### 1. Configure AWS access profile
    aws configure --profile aws-dev-infra

##### 2. Create new ssh key for this environment

    aws ec2 create-key-pair \
        --key-name dev \
        --query 'KeyMaterial' \
        --output text > aws-dev-infra.pem
    mv aws-dev-infra.pem ~/.ssh/
    chmod 600 ~/.ssh/aws-dev-infra.pem

##### 3. Create state bucket for terraform
    aws s3 mb s3://terraform-dev-crm --region us-east-1 --profile aws-dev-infra

##### 4. Decrypt terraform variable file with sops

    export SOPS_AGE_KEY_FILE="~/work/techork/sops/key"
    export SOPS_AGE_RECIPIENTS=age166r5dtedlc4y8nq50d8qp60z72r4w833l6rmwv4kg76j5833gapqv2x0a2
    sops -d enc.variables > variables.tf

##### 5. Initialize terraform
    terraform init

##### 6. Review terraform plan
    terraform plan

##### 7. Apply the plan and save the output
    terraform apply

