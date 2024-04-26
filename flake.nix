{
  description = "Development environment for the project";

  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixos-23.11-small";
    systems.url = "github:nix-systems/default";
    flake-compat.url = "github:nix-community/flake-compat";
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
            # just to make it easier
            altEditor = true;
            extra = true;
          in
          pkgs.mkShell {
            packages = with pkgs; [
              php82
              php82Packages.composer
            ]
            ++ (if altEditor then [ pkgs.neovim pkgs.lunarvim ] else [ ])
            ++ (if extra then [ pkgs.nodejs_18 ] else [ ]);

            shellHook = ''
              PATH="$PATH:$HOME/.local/bin"

              echo "ready to rock! 🚀"
            '';
          };
      });

      formatter.x86_64-linux = nixpkgs.legacyPackages.x86_64-linux.nixpkgs-fmt;
    };
}
