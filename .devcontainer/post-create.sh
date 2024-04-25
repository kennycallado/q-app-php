#! /usr/bin/env bash

mkdir -p "${HOME}/.local/bin"
mkdir -p "${HOME}/.config/lvim"
PATH="${PATH}:${HOME}/.local/bin"

echo "Installing needed tools"

# ripgrep
curl -LO https://github.com/BurntSushi/ripgrep/releases/download/13.0.0/ripgrep_14.0.0_amd64.deb
dpkg -i ripgrep_14.0.0_amd64.deb

# surrealdb cli
curl --proto '=https' --tlsv1.2 -sSf https://install.surrealdb.com | sh

echo "Installing LunarVim"
# neovim
bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)

# lunarvim
LV_BRANCH='release-1.3/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.3/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y

# lvim config
ln -sf $PWD/.devcontainer/lvim-config.lua $HOME/.config/lvim/config.lua && set +x

exit 0
