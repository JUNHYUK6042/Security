# DNS 위임

## 

```
192.168.11.214 : root
192.168.11.215 : sec.
192.168.11.216 : ast06.sec.
192.168.11.217 : cache
```

---

## Cache 서버 설정

### named.conf

```text
options {
        directory     "/var/named";
};

zone "." IN {
 type hint;
 file "named.ca";
};
```

### named.ca

- `Root`에 대해 모르면  
`192.168.11.214`에 있는 `a.root-servers.net`에게 가서 물어보는 것입니다.

```text
.                       518400  IN      NS      a.root-servers.net.
a.root-servers.net.     518400  IN      A       192.168.11.214
```

---

## Root Dns 서버 설정

### named.conf

```text
options {
 directory "/var/named";
};

zone "." {
     type master;
     file "root.zone";
};
```

### zone File 설정

```
$TTL    1D
@   IN   SOA  ns.   root.ns. (
                                   0         ; Serial
                                   1D        ; Refresh
                                   1H        ; Retry
                                   1W        ; Expire
                                   3H )      ; Minimum
; Name Server
   IN   NS      ns.
; Host address
ns          IN     A       192.168.11.214
; Sub Domain
sec.     IN    NS   ns.sec.
ns.sec.  IN    A    192.168.11.215
```

### named.ca

```text
.                       518400  IN      NS      a.root-servers.net.
a.root-servers.net.     518400  IN      A       192.168.11.214
```

---

## ns.sec. DNS 서버 설정

### named.conf

```text
options {
 directory "/var/named";
};

zone "sec." {
     type master;
     file "sec.zone";
};
```

### zone File 설정

```
$TTL    1D
@   IN   SOA  ns.sec.   root.ns.sec. (
                                   0         ; Serial
                                   1D        ; Refresh
                                   1H        ; Retry
                                   1W        ; Expire
                                   3H )      ; Minimum
; Name Server
   IN   NS      ns.sec.
; Host address
ns          IN     A       192.168.11.215
; Sub Domain
ast06.sec.     IN    NS   ns.ast06.sec.
ns.ast06.sec.  IN    A    192.168.11.216
```

### named.ca

```text
.                       518400  IN      NS      a.root-servers.net.
a.root-servers.net.     518400  IN      A       192.168.11.214
```

---

## ns.sec. DNS 서버 설정

### named.conf

```text
options {
 directory "/var/named";
};

zone "sec." {
     type master;
     file "sec.zone";
};
```

### zone File 설정

```
$TTL    1D
@   IN   SOA  ns.as06.sec.   root.ns.ast06.sec. (
                                   0         ; Serial
                                   1D        ; Refresh
                                   1H        ; Retry
                                   1W        ; Expire
                                   3H )      ; Minimum
; Name Server
   IN   NS      ns.ast06.sec.
; Host address
ns          IN     A       192.168.11.216
; 
```

### named.ca

```text
.                       518400  IN      NS      a.root-servers.net.
a.root-servers.net.     518400  IN      A       192.168.11.214
```

---

## DNS 서버 질의

