import configApi from "@/globalConfig";
export default function(Vue) {
  Vue.configs = {
    appName: configApi.appName,
    apiUrl: configApi.apiUrl,
    apiAssetsUrl: configApi.apiAssetsUrl,
  };
  Object.defineProperties(Vue.prototype, {
    $configs: {
      get() {
        return Vue.configs;
      }
    }
  });
}