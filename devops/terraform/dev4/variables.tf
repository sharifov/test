# Variables
variable "REGION" {
  default     = "us-east-1"
  description = "AWS Region"
  type        = string
}

variable "VPC_ID" {
  default     = "vpc-0a4b73e5e225e3659"
  description = "VPC ID"
  type        = string
}

variable "VPC_CIDR" {
  default     = "10.0.0.0/16"
  description = "VPC Subnet Range"
  type        = string
}

variable "PRIVATE_SUBNETS" {
  default     = ["subnet-0d9b21b5164bf13da", "subnet-07752f84cf7214285", "subnet-052f6491dca016d63"]
  description = "Private Subnet Range"
  type        = list(string)
}

variable "PUBLIC_SUBNETS" {
  default     = ["subnet-073f1c7be18ad078a", "subnet-0206d5506029f28a8", "subnet-0e9cd3cd299f47806"]
  description = "Public Subnet Range"
  type        = list(string)
}

variable "PROJECT" {
  default     = "crm"
  description = "Project name"
  type        = string
}

variable "ENV" {
  default     = "dev4"
  description = "Environment name"
  type        = string
}

variable "NAMESPACE" {
  default     = "kiv"
  description = "Namespace"
  type        = string
}

variable "DOMAIN" {
  default     = "crm.dev4.travel-dev.com"
  description = "Domain name"
  type        = string
}

variable "SSH_KEY" {
  type        = string
  description = "SSH Access Key"
  default     = "aws-dev-ssh"
}

variable "INFRA_CIDR" {
  type        = string
  description = "Infra account VPC CIDR"
  default     = "10.150.0.0/16"
}

variable "ASG_SIZE" {
  type        = string
  description = "Autoscaling group size"
  default     = "1"
}

variable "APP_INSTANCE_TYPE" {
  type        = string
  description = "EC2 instance type"
  default     = "t3.medium"
}

variable "APP_AMI" {
  type        = string
  description = "AMI"
  default     = "ami-04505e74c0741db8d"
}

variable "SHARED_INSTANCE_TYPE" {
  type        = string
  description = "EC2 instance type"
  default     = "t3.medium"
}

variable "SHARED_AMI" {
  type        = string
  description = "AMI"
  default     = "ami-04505e74c0741db8d"
}

variable "MYSQL_RDS_INSTANCE_TYPE" {
  type        = string
  description = "EC2 instance type"
  default     = "db.t2.medium"
}

variable "MYSQL_RDS_DATABASE" {
  type        = string
  description = "Database"
  default     = "crm"
}

variable "MYSQL_RDS_USERNAME" {
  type        = string
  description = "Username"
  default     = "crm"
}

variable "MYSQL_RDS_PASSWORD" {
  type        = string
  description = "Password"
  default     = "9TBPBr654D5Hsmc348jz"
}

variable "PGSQL_RDS_INSTANCE_TYPE" {
  type        = string
  description = "EC2 instance type"
  default     = "db.t2.medium"
}

variable "PGSQL_RDS_DATABASE" {
  type        = string
  description = "Database"
  default     = "crm"
}

variable "PGSQL_RDS_USERNAME" {
  type        = string
  description = "Username"
  default     = "crm"
}

variable "PGSQL_RDS_PASSWORD" {
  type        = string
  description = "Password"
  default     = "vzbXqsxL93JFCQJ6LHLb"
}
