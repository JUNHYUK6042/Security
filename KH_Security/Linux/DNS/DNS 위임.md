# DNS 위임 서버 구축

## DNS 서버 실습 구성

```
192.168.11.214 : Root Name Server (루트 존(.) 관리 및 TLD 서버 정보 제공)

192.168.11.215 : TLD Name Server (sec. 도메인의 네임서버)

192.168.11.216 : Sub Domain Name Server (ast06.sec. 서브 도메인의 네임서버)

192.168.11.217 : Cache Name Server (존을 가지지 않고 질의 결과를 캐시하여 응답)
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

```text
.                       518400  IN      NS      a.root-servers.net.
a.root-servers.net.     518400  IN      A       192.168.11.214
```

- `Root`에 대해 모르면  
`192.168.11.214`에 있는 `a.root-servers.net`에게 가서 물어보는 것입니다.

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

- **sec.     IN    NS   ns.sec.**
  - `sec.` 서브 도메인의 권한 있는 네임서버는 ns.sec.고,  
sec.에 대한 질문은 내가 아니라 ns.sec.에게 물어보라는 뜻 입니다.

- **ns.sec.  IN    A    192.168.11.215**
  - ns.sec.라는 네임서버의 실제 IP 주소는 192.168.11.215  
  DNS가 ns.sec.를 찾을 수 있도록 Glue Record 역할을 합니다.
  - 안 그러면 “ns.sec.가 누구야?” 하다가 무한 루프 걸리게 됩니다.

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
- **ast06.sec.     IN    NS   ns.ast06.sec.**
  - `ast06.sec.` 서브 도메인의 권한 있는 네임서버는 ns.ast06.sec.고,  
ast06.sec.에 대한 질문은 내가 아니라 ns.ast06.sec.에게 물어보라는 뜻 입니다.

- **ns.ast06.sec.  IN    A    192.168.11.216**
  - ns.ast06.sec.라는 네임서버의 실제 IP 주소는 192.168.11.216  
  DNS가 ns.ast06.sec.를 찾을 수 있도록 Glue Record 역할을 합니다.
  - 안 그러면 “ns.ast06.sec.가 누구야?” 하다가 무한 루프 걸리게 됩니다.


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

- 마지막으로 `ast06.sec` 도메인의 네임서버 IP 주소는 `192.168.11.216` 입니다

### named.ca

```text
.                       518400  IN      NS      a.root-servers.net.
a.root-servers.net.     518400  IN      A       192.168.11.214
```

---

## DNS 서버 질의

- 캐시 네임서버에서 다음 질의를 수행합니다.
```
nslookup ns.
nslookup ns.sec
nslookup ns.ast06.sec
```
- 캐시 네임서버는 `루트 네임서버 → TLD → 하위 도메인` 순으로 질의합니다.

![01](/KH_Security/Linux/DNS/img/14.png)

- 정상적으로 IP 주소가 응답이 오는 것을 확인할 수 있습니다.
- Local Dns Server는 Cache Name Server로 하였습니다.
  Cache Name Server의 IP 주소는 192.168.11.217에게 물어본 것을 확인할 수 있습니다.
