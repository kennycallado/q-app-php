#! /usr/bin/env bash

mkdir -p "$HOME/.local/bin"
PATH="$PATH:$HOME/.local/bin"

bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)
LV_BRANCH='release-1.3/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.3/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y

ln -sf $PWD/.devcontainer/lvim-config.lua $HOME/.config/lvim/config.lua && set +x

exit 0
