
## TODO:

- [X] change route users/:id for parti/:id
- [ ] models: relation should be object|string instead of object. repos will no construct the object

- [X] iAuth: should be changed to pAuth meaning project authentication
- [~] interventions: change permissions for 'admin', 'coord', 'thera' ??

- [ ] modal, popup, errors:
  - `$_SESSION['popup'] = json_encode(json_encode((object) ["body" => "User created. Waiting for admin approval.", "type" => "success"]));`
  - `$_SESSION['error'] = json_encode($auth->error);`
- [ ]
