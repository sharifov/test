CRM: Ansible Playbooks
-------------------------------------------

# Provision CRM application server:

    ansible-playbook app.yml \
        -i inventories/dev-new/aws_ec2.yml \
        --vault-password-file=~/.ansible/vaultfile

# Provision CRM shared server:

    ansible-playbook app.yml \
        -i inventories/dev-new/aws_ec2.yml \
        --vault-password-file=~/.ansible/vaultfile

# Deploy a test release:

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
        -i inventories/dev-new/aws_ec2.yml \
        --vault-password-file=~/.ansible/vaultfile \
        -e app_ver=test

# Update dotenv for dev environment:

    ansible-vault edit inventories/dev/group_vars/all/secrets.yml \
        --vault-password-file=~/.ansible/vaultfile
