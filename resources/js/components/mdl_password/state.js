export let config = {
    title: null,
    subtitle: null,
    onSuccess: null
};

export let statePassword = false;

export function setConfig(instance) {
    config = instance;
}

export function getConfig() {
    return config;
}

export function setStatePassword(_statePassword) {
    statePassword = _statePassword;
}

export function getStatePassword() {
    return statePassword;
}
