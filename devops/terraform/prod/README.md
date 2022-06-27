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
```
aws configure --profile aws-prod-infra
```

##### 2. Decrypt terraform variable file with sops

````
export SOPS_AGE_KEY_FILE="$HOME/.sops/aws-prod-age"
export SOPS_AGE_RECIPIENTS="age166r5dtedlc4y8nq50d8qp60z72r4w833l6rmwv4kg76j5833gapqv2x0a2"
sops -d enc.variables > variables.tf
```

##### 3. Initialize terraform

```
terraform init
```

##### 4. Review terraform plan

```
terraform plan
```

##### 5. Apply the plan and save the output

```
terraform apply
```

##### 6. Generate keys for s3 bucket

```
aws iam create-access-key \
    --user-name attachments-crm-prod \
    --profile aws-prod-infra
```
