# tt-Imagick


imagemagickのインストール

```
brew install pkg-config 
brew install imagemagick 
brew install gs
```

peclでimagickのインストール

```
pecl install imagick
```

上記で入らない場合

```
$ git clone https://github.com/Imagick/imagick
$ cd imagick
$ phpize && ./configure
$ make
$ make install
```

