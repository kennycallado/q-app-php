{
  description = "Development environment for the project";

  inputs = {
    flake-compat.url = "github:nix-community/flake-compat";
    nixpkgs.url = "github:nixos/nixpkgs/nixos-23.11-small";
    systems.url = "github:nix-systems/default";
  };

  outputs = { nixpkgs, systems, ... }:
    let
      eachSystem = nixpkgs.lib.genAttrs (import systems);
    in
    {

      devShells = eachSystem (system: {
        default =
          let
            pkgs = import nixpkgs { inherit system; };
          in
          pkgs.mkShell {
            packages = with pkgs; [
              # dependencies
              php82
              php82Packages.composer

              # other tools
              # surrealdb
              gh
            ];

            shellHook = ''
              # {{{ load .env file
              if [ -f .env ]; then
                export $(cat .env | xargs)
              fi
              # }}}
              
              # {{{ set up surreal alias through docker or podman
              if command -v docker &> /dev/null; then
                CONTAINER_BIN=docker
              elif command -v podman &> /dev/null; then
                CONTAINER_BIN=podman
              fi

              if [ -z $CONTAINER_BIN ]; then
                echo -e "\033[0;31m===\033[0m Warning \033[0;31m===\033[0m"
                echo "Neither docker nor podman is installed"
              else
                alias surreal="$CONTAINER_BIN exec -it $DB_HOST /surreal"
              fi
              # }}}

              # {{{ check services are running surrealdb|app|auth|super
              if [ -z $CONTAINER_BIN ]; then
                echo -e "\033[0;31m===\033[0m Warning \033[0;31m===\033[0m"
                echo "Unable to check services"
              else
                if [[ -z $($CONTAINER_BIN ps -a --format "table {{.Names}}" | grep "surrealdb") ]]; then
                  echo "surrealdb service is not running"
                else
                  SERVICES_RUNNING=false
                fi

                if [[ -z $($CONTAINER_BIN ps -a --format "table {{.Names}}" | grep "super") ]]; then
                  echo "super service is not running"
                else
                  SERVICES_RUNNING=false
                fi

                if [[ -z $($CONTAINER_BIN ps -a --format "table {{.Names}}" | grep "auth") ]]; then
                  echo "auth service is not running"
                else
                  SERVICES_RUNNING=false
                fi

                if [[ -z $($CONTAINER_BIN ps -a --format "table {{.Names}}" | grep "app") ]]; then
                  echo "app service is not running"
                else
                  SERVICES_RUNNING=false
                fi

                if [ -z $SERVICES_RUNNING ]; then
                  echo -e "\033[0;31m===\033[0m Warning \033[0;31m===\033[0m"
                  echo "Services are not running"
                else
                  echo -e "\033[0;32m===\033[0m Services \033[0;32m===\033[0m"
                  echo "Services are running"
                fi
              fi
              # }}}

              # {{{ install composer dependencies
              if [ -f composer.json ]; then
                echo -e "\033[0;33m===\033[0m Installing composer dependencies \033[0;33m===\033[0m"
                composer install
              fi
              # }}}

              # {{{ welcome message
              echo -e "\033[0;32m===\033[0m Welcome \033[0;32m===\033[0m"
              echo "ready to rock! 🚀"
              # }}}
            '';
          };
      });

      formatter.x86_64-linux = nixpkgs.legacyPackages.x86_64-linux.nixpkgs-fmt;
    };
}
