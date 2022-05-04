CRM: Ansible Playbooks
-------------------------------------------

# Provision CRM application server (12 minutes):

```
ansible-playbook app.yml \
    -i inventories/dev/aws_ec2.yml \
    --vault-password-file=$HOME/.ansible/vaultfile-dev \
    --private-key $HOME/.ssh/aws-dev-ssh.pem
```

# Provision CRM shared server (12 minutes):

```
ansible-playbook shared.yml \
    -i inventories/dev/aws_ec2.yml \
    --vault-password-file=$HOME/.ansible/vaultfile-dev \
    --private-key $HOME/.ssh/aws-dev-ssh.pem
```

# Deploy a test release (2 minutes):

```
# Go to application root directory
cd ../../

# Install application dependencies
composer install --no-interaction --ignore-platform-reqs

# Create an artifact and put it inside deployment/files
tar --exclude=./devops \
    -czf devops/ansible/roles/deployment/files/build.tar.gz .

# Go back to ansible dir
cd devops/ansible

# Run deploy.yml and set app_ver to test
ansible-playbook deploy.yml \
    -i inventories/dev/aws_ec2.yml \
    --vault-password-file=$HOME/.ansible/vaultfile-dev \
    --private-key $HOME/.ssh/aws-dev-ssh.pem \
    -e app_ver=test
```

# Update dotenv for dev environment:

```
ansible-vault edit inventories/dev/group_vars/all/secrets.yml \
    --vault-password-file=$HOME/.ansible/vaultfile-dev
```
