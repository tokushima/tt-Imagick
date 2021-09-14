# tt-image


## imagemagickのインストール

```
brew install pkg-config 
brew install imagemagick 
brew install gs
```

## peclでimagickのインストール

```
pecl install imagick
```

pcre2が無いと言われたら

```
ln -s /opt/homebrew/Cellar/pcre2/10.37_1/include/pcre2.h /opt/homebrew/Cellar/php/8.0.9/include/php/ext/pcre/pcre2.h
```

### peclで入らない場合

```
$ git clone https://github.com/Imagick/imagick
$ cd imagick
$ phpize && ./configure
$ make
$ make install
```


## VS CodeのPHP Intelephense対応

VS Code - 管理 - 拡張機能 - PHP Intelephense - 管理(右下アイコン) - 拡張機能の設定 - Intelephense: Stubs で項目の追加 - imagick



