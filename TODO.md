
## TODO:

- [ ] media: videos should be the youtube id? or extract it from the url?

- [ ] certs: for https when condespaces
  - [research](https://github.com/BirgerK/docker-apache-letsencrypt)
- [ ] routes: ?? admin/centers to centers

- [X] aside: resources should be an accordion
- [ ] sleep: some actions that involves super needs some delay...

- [X] build: there is no vendor, should execute composer
- [~] build: unable to create arm image with intl
  - needs qemu-user-static but don't know how to get it in nixos
  - [X] use github actions as work around

- [X] parti: details shows info by tabs
  - papers
  - scores

- [X] change route users/:id for parti/:id
- [ ] models: relation should be object|string instead of object. repos will no construct the object

- [X] iAuth: should be changed to pAuth meaning project authentication
- [~] interventions: change permissions for 'admin', 'coord', 'thera' ??

- [ ] modal, popup, errors:
  - `$_SESSION['popup'] = json_encode(json_encode((object) ["body" => "User created. Waiting for admin approval.", "type" => "success"]));`
  - `$_SESSION['error'] = json_encode($auth->error);`
- [ ]
