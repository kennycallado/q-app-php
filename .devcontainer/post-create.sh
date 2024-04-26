#! /usr/bin/env bash

# mkdir -p "${HOME}/.local/bin"
# mkdir -p "${HOME}/.config/lvim"
# PATH="${PATH}:${HOME}/.local/bin"

echo "Installing needed tools"

# surrealdb cli
# bash <(curl --proto '=https' --tlsv1.2 -sSf https://install.surrealdb.com) --version v1.3.1
# mv ${HOME}/.surrealdb/surreal ${HOME}/.local/bin/surreal

echo "Installing LunarVim"

# neovim
# bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)

# lunarvim
# LV_BRANCH='release-1.3/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.3/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y

# lunarvim config
# ln -sf $PWD/.devcontainer/lvim-config.lua $HOME/.config/lvim/config.lua && set +x

alias surreal="docker exec -it q-app-php_devcontainer-db-1 /surreal"

exit 0
