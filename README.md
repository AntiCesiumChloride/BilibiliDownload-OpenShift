# BilibiliDownload for Openshift

在 Openshift 快速创建一个 BilibiliDownload。你需要拥有一个 OpenShift Online 账号

## 安装方式

### 手动安装

```bash
rhc app create bilidown php-5.4 --from-code=https://github.com/fuckbilibili/BilibiliDownload-OpenShift.git
```

### 在线安装

前往安装 [PHP 5.4 Cartridge](https://openshift.redhat.com/app/console/application_type/cart!php-5.4)，在 Source Code 里输入 `https://github.com/fuckbilibili/BilibiliDownload-OpenShift.git` 即可。

## 其它环境

支持 PHP 5.4 环境，需要开启 cURL 和 APC 支持，使用时请删除 `.openshift` 文件夹。