FROM ubuntu:latest

RUN apt-get -y update
RUN apt-get install -y --no-install-recommends apt-utils
RUN apt-get install -y php
RUN apt-get install -y nodejs npm
RUN update-alternatives --install /usr/bin/node node /usr/bin/nodejs 10
RUN apt-get install -y default-jre
RUN apt-get install -y default-jdk
RUN apt-get -y autoremove && apt-get clean && apt-get autoclean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD mkdir -p ~/runners
