{pkgs}: {
  deps = [
    pkgs.php82Extensions.intl
    pkgs.php82Extensions.zip
    pkgs.php82Extensions.xml
    pkgs.php82Extensions.mbstring
    pkgs.php82Extensions.curl
    pkgs.php82Extensions.gd
    pkgs.php82Extensions.mysqli
    pkgs.php82
    pkgs.unzip
    pkgs.wget
    pkgs.mariadb
  ];
}
