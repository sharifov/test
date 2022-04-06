CRM: Terraform
-------------------------------------------

### Prerequisite ###

* AWS Account with admin privileges
* Route53 Hosted Zone
* Linux environment to run the code
* Terraform v1.1.6

### Infrastructure deployment

##### 1. Configure AWS access profile
    aws configure --profile aws-stage-infra

##### 2. Create new ssh key for this environment

    aws ec2 create-key-pair \
        --key-name stage \
        --query 'KeyMaterial' \
        --output text > aws-stage-infra.pem
    mv aws-stage-infra.pem ~/.ssh/
    chmod 600 ~/.ssh/aws-stage-infra.pem

##### 3. Create state bucket for terraform
    aws s3 mb s3://terraform-stage3383-crm --region us-east-1 --profile aws-stage-infra

##### 4. Initialize terraform
    terraform init

##### 5. Review terraform plan
    terraform plan

##### 6. Apply the plan and save the output
    terraform apply

