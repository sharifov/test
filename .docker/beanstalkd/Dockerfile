FROM ubuntu:20.04

ARG version

RUN apt-get update \
    && apt-get install -y build-essential curl \
    && curl -sL https://github.com/beanstalkd/beanstalkd/archive/v$version.tar.gz | tar xvz -C /tmp \
    && cd /tmp/beanstalkd-$version \
    && make \
    && cp beanstalkd /usr/bin

ENTRYPOINT ["/usr/bin/beanstalkd"]