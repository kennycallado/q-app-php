# FROM mcr.microsoft.com/devcontainers/base:alpine

# Install neovim
# RUN apk add --no-cache neovim

# # Install next dependencies
# RUN apk add --no-cache \
#   bash \
#   ripgrep \
#   jq

#     openssh \
#     git \
#     curl \
#     jq \
#     make \
#     nodejs \
#     npm \
#     cargo \
#     python3 \
#     py3-pip \
#     ripgrep \
#     neovim

# FROM debian:bookworm
FROM mcr.microsoft.com/devcontainers/base:debian

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive && \
  apt-get -y install --no-install-recommends \
  ripgrep \
  jq

  # ca-certificates \
  # git \
  # curl \
  # make \
  # gcc \

#  git \
#  curl \
#  make \
#  gcc \

# Clean up apt
RUN apt-get autoremove -y && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# USER vscode

# Create a directory for the LunarVim configuration
# RUN mkdir -p "${HOME}/.local/bin"
# ENV PATH="${PATH}:${HOME}/.local/bin"

# Install neovim
# RUN bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)
# RUN INSTALL_PREFIX="/usr/local" bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)

# Install LunarVim 
# RUN LV_BRANCH='release-1.3/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.3/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y
# RUN INSTALL_PREFIX="/usr/local" LV_BRANCH='release-1.3/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.3/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y

# RUN LV_BRANCH='release-1.2/neovim-0.8' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.2/neovim-0.8/utils/installer/install.sh) --no-install-dependencies -y
