# App instance
resource "aws_instance" "app" {
  count                  = var.ASG_SIZE
  ami                    = var.APP_AMI
  instance_type          = var.APP_INSTANCE_TYPE
  key_name               = var.SSH_KEY
  subnet_id              = var.PRIVATE_SUBNETS[0]
  vpc_security_group_ids = [aws_security_group.app.id]

  root_block_device {
    volume_size = var.APP_VOLUME_SIZE
    volume_type = "gp3"
    encrypted   = false
  }

  tags = {
    Name        = "app${count.index}-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
    API         = "api.{var.DOMAIN}"
    App         = var.PROJECT
    Kind        = "app"
    Monitoring  = "prometheus"
    Terraform   = "true"
  }
}

# App Target Group
resource "aws_lb_target_group" "app" {
  name     = "app-${var.PROJECT}-${var.ENV}"
  port     = 8443
  protocol = "HTTPS"
  vpc_id   = var.VPC_ID

  health_check {
    path                = "/"
    port                = "traffic-port"
    timeout             = 3
    interval            = 5
    healthy_threshold   = 4
    unhealthy_threshold = 2
    matcher             = "200-400"
  }
}

resource "aws_lb_target_group_attachment" "app" {
  count            = length(aws_instance.app)
  target_group_arn = aws_lb_target_group.app.arn
  target_id        = aws_instance.app[count.index].id
}

# API Target Group
resource "aws_lb_target_group" "api" {
  name     = "api-${var.PROJECT}-${var.ENV}"
  port     = 9443
  protocol = "HTTPS"
  vpc_id   = var.VPC_ID
}

resource "aws_lb_target_group_attachment" "api" {
  count            = length(aws_instance.app)
  target_group_arn = aws_lb_target_group.api.arn
  target_id        = aws_instance.app[count.index].id
}

# WS Target Group
resource "aws_lb_target_group" "ws" {
  name     = "ws-${var.PROJECT}-${var.ENV}"
  port     = 8080
  protocol = "HTTP"
  vpc_id   = var.VPC_ID

  stickiness {
    cookie_duration = 86400
    type            = "lb_cookie"
  }

  health_check {
    path                = "/"
    port                = "traffic-port"
    timeout             = 3
    interval            = 5
    healthy_threshold   = 4
    unhealthy_threshold = 2
    matcher             = "400"
  }
}

resource "aws_lb_target_group_attachment" "ws" {
  count            = length(aws_instance.app)
  target_group_arn = aws_lb_target_group.ws.arn
  target_id        = aws_instance.app[count.index].id
}

# Centrifugo Target Group
resource "aws_lb_target_group" "centrifugo" {
  name     = "centrifugo-${var.PROJECT}-${var.ENV}"
  port     = 8000
  protocol = "HTTP"
  vpc_id   = var.VPC_ID

  stickiness {
    cookie_duration = 86400
    type            = "lb_cookie"
  }

  health_check {
    path                = "/health"
    port                = "traffic-port"
    timeout             = 3
    interval            = 5
    healthy_threshold   = 4
    unhealthy_threshold = 2
    matcher             = "200"
  }
}

resource "aws_lb_target_group_attachment" "centrifugo" {
  target_group_arn = aws_lb_target_group.centrifugo.arn
  target_id        = aws_instance.shared.id
}

# App Security Group
resource "aws_security_group" "app" {
  name        = "private-${var.PROJECT}-${var.ENV}"
  description = "Allows internal communication betwen ALB and TG"
  vpc_id      = var.VPC_ID

  ingress {
    description = "Self VPC Security Group"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    self        = true
  }

  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "APP"
    from_port   = 8443
    to_port     = 8443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "API"
    from_port   = 9443
    to_port     = 9443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "Filebeat"
    from_port   = 5066
    to_port     = 5066
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Node Exporter"
    from_port   = 9100
    to_port     = 9100
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
  }

  ingress {
    description = "Beats Exporter"
    from_port   = 9479
    to_port     = 9479
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
  }

  ingress {
    description = "PHPFPM Exporter"
    from_port   = 9280
    to_port     = 9280
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "private-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
    Terraform   = "true"
  }
}
