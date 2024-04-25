#! /usr/bin/env bash

platforms=("linux/arm64" "linux/amd64")

package_name=$(jq -r '.name' composer.json | sed 's/^.*\/\+//')
package_version=$(git describe --tags --abbrev=0)

main() {
    install_dependencies
    testing
    check_images

    for platform in "${platforms[@]}"; do
        tag=$(echo "${platform//\//_}" | tr -d 'linux_' | xargs -I {} echo {})
        image_name="kennycallado/${package_name}:${package_version}-${tag}"

        echo -e "Building image:\n\t$image_name"

        podman_build $tag $platform $image_name
    done

    podman_manifests $package_version
}

podman_manifests() {
    versions=("latest" "$1")

    for version in "${versions[@]}"; do
        manifest_name="kennycallado/${package_name}:${version}"
        podman manifest create --amend $manifest_name

        for platform in "${platforms[@]}"; do
            tag=$(echo "${platform//\//_}" | tr -d 'linux_' | xargs -I {} echo {})
            image_name="kennycallado/${package_name}:${package_version}-${tag}"

            podman manifest add --arch $tag $manifest_name $image_name
            podman manifest push --all $manifest_name

            podman rmi $image_name
        done
    done
}

podman_build() {
  local tag=$1
  local platform=$2
  local image_name=$3

  podman build --no-cache --pull \
    --platform ${platform} \
    -t kennycallado/${package_name}:${package_version}-${tag} \
    --build-arg ENVIRONMENT=production \
    -f ./Containerfile .

  podman push kennycallado/${package_name}:${package_version}-${tag}
  podman rmi  kennycallado/${package_name}:${package_version}-${tag}
}

check_images() {
    for platform in "${platforms[@]}"; do
        tag=$(echo "${platform//\//_}" | tr -d 'linux_' | xargs -I {} echo {})
        image_name="kennycallado/${package_name}:${package_version}-${tag}"

        echo -e "Checking if exists image:\n\t$image_name"

        if podman image exists $image_name; then
            echo "Removing old image..."
            podman rmi $image_name
        fi
    done

    image_name="kennycallado/${package_name}:latest"
    if podman image exists $image_name; then
        echo "Removing old image..."
        podman rmi $image_name
    fi

    image_name="kennycallado/${package_name}:${package_version}"
    if podman image exists $image_name; then
        echo "Removing old image..."
        podman rmi $image_name
    fi
}

install_dependencies() {
    echo "Installing dependencies..."

    podman run --rm --interactive --tty --volume .:/app composer install --no-dev --optimize-autoloader
}

testing() {
    echo "Running tests..."

    podman run --rm -v .:/app php:8.2-cli-alpine /app/vendor/bin/phpunit /app/tests
}

main "$@"
