# Cisco Router Redistribution

## 개요
- RIP는 **STATIC / OSPF / EIGRP** 로부터 라우팅 정보를 Redistribution 받을 수 있다.
- RIP는 metric으로 **Hop Count** 를 사용하며, 최대 값은 **15** 이다.
- metric 값이 **16 이상이면 RIP는 해당 경로를 무시**한다.

---

## Redistribution - RIP

### RIP : STATIC, OSPF, EIGRP

- `redistribute static` 명령은 default route 설정을 포함한다.
- Metric
  - 다른 라우팅 프로토콜로부터 이전된 경로의 metric 값은 RIP과 형식이 달라
    경로가 무시될 수 있으므로 16 이하의 적당한 값을 입력한다.
  - 전달된 경로의 metric 값이 16보다 크면 RIP는 이를 무시한다.
  - Default-metric을 지정하면 metric 입력을 생략할 수 있다.

### Command Format

```bash
redistribute static
redistribute ospf <PID> metric <값>
redistribute eigrp <AS> metric <값>
```

### Ex

```bash
redistribute static
redistribute ospf 1 metric 5
redistribute eigrp 100 metric 5
```

---

## Redistribution - OSPF

### OSPF : STATIC, RIP, EIGRP

- `redistribute static subnets` 명령은 default route 설정을 포함하지 않는다.
- OSPF에서 default route를 전달하기 위해
  `default-information originate` 명령이 반드시 필요하다.
- Metric
  - 다른 라우팅 프로토콜로부터 이전된 경로의 metric 값은
    OSPF와 형식이 다르므로 값을 직접 지정한다.
- Subnets
  - classless 정보를 전송하기 위해 사용된다.

### Command Format

```bash
redistribute static subnets
redistribute rip subnets
redistribute eigrp <AS> metric <값> subnets
```

### Ex

```bash
redistribute static subnets
redistribute rip subnets
redistribute eigrp 100 metric 10 subnets
```

### Default Route

```bash
default-information originate
```

---

## Redistribution - EIGRP

### EIGRP : STATIC, RIP, OSPF

- `redistribute static` 명령은 default route 설정을 포함한다.
- Metric
  - default-metric 명령으로 BW / Delay / Reliability / Load / MTU 값을 대신할 수 있다.

### EIGRP Metric 구성 요소

| K 값 | 항목         | 설명           |
|------|--------------|----------------|
| K1   | Bandwidth    | 대역폭         |
| K3   | Delay        | 지연           |
| K4   | Reliability  | 신뢰도         |
| K2   | Load         | 회선 사용량    |
| K5   | MTU          | 최대 전송 단위 |

Metric 입력 순서 : K1 K3 K4 K2 K5

### Command Format

```bash
redistribute static
redistribute rip metric <Bandwidth> <Delay> <Reliability> <Load> <MTU>
redistribute ospf <Process-ID> metric <Bandwidth> <Delay> <Reliability> <Load> <MTU>
```

### Ex

```bash
redistribute static
redistribute rip metric 10000 10 255 1 1500
redistribute ospf 1 metric 10000 1000 255 1 1500
```

---

## Static-RIP

### Static-RIP 구성도

![01](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/01.png)

---

### Static Setting

#### R1

```
ip route 0.0.0.0 0.0.0.0 12.1.1.2
```

#### R2

```
ip route 0.0.0.0 0.0.0.0 23.1.1.3
ip route 1.1.1.0 255.255.255.0 12.1.1.1
```

#### R3

```
ip route 1.1.1.0 255.255.255.0 23.1.1.2
ip route 2.2.2.0 255.255.255.0 23.1.1.2
ip route 12.1.1.0 255.255.255.0 23.1.1.2

- Redistribute Setting -
router rip
redistribute static
```

---

### RIP Setting

#### R3

```
router rip
version 2
no auto-summary
network 23.1.1.0
network 34.1.1.0
```

#### R4

```
router rip
version 2
no auto-summary
passive-interface g0/0
network 4.4.4.0
network 34.1.1.0
network 45.1.1.0
```

#### R5

```
router rip
version 2
no auto-summary
passive-interface g0/0
network 5.5.5.0
network 45.1.1.0
```

---

### PC에서의 통신 테스트

- PC1(1.1.1.11)  
![02](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/02.png)

- PC2(2.2.2.11)  
![03](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/03.png)

- PC3(4.4.4.11)  
![04](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/04.png)

- PC4(5.5.5.11)  
![05](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/05.png)

---

## Static-OSPF

### Static-OSPF 구성도
  
![06](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/06.png)

---

### Static Setting

#### R1

```
ip route 0.0.0.0 0.0.0.0 12.1.1.2
```

#### R2

```
ip route 0.0.0.0 0.0.0.0 23.1.1.3
ip route 1.1.1.0 255.255.255.0 12.1.1.1
```

#### R3

```
ip route 1.1.1.0 255.255.255.0 23.1.1.2
ip route 2.2.2.0 255.255.255.0 23.1.1.2
ip route 12.1.1.0 255.255.255.0 23.1.1.2
```

---

### OSPF Setting

#### R3

```
router ospf 1
network 23.1.1.3 0.0.0.0 area 0
network 34.1.1.3 0.0.0.0 area 0

- Redistribute Setting -
redistribute static subnets
default-information originate
```

#### R4

```
router ospf 1
passive-interface g0/0
network 4.4.4.1 0.0.0.0 area 0
network 34.1.1.4 0.0.0.0 area 0
network 45.1.1.4 0.0.0.0 area 0
```

#### R5

```
router ospf 1
passive-interface g0/0
network 5.5.5.1 0.0.0.0 area 0
network 45.1.1.5 0.0.0.0 area 0
```

---

### PC에서의 통신 테스트

- PC1(1.1.1.11)  
![07](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/07.png)

- PC2(2.2.2.11)  
![08](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/08.png)

- PC3(4.4.4.11)  
![09](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/09.png)

- PC4(5.5.5.11)  
![10](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/10.png)

---

## Static-EIGRP

### Static-EIGRP 구성도
  
![11](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/11.png)

---

### Static Setting

#### R1

```
ip route 0.0.0.0 0.0.0.0 12.1.1.2
```

#### R2

```
ip route 0.0.0.0 0.0.0.0 23.1.1.3
ip route 1.1.1.0 255.255.255.0 12.1.1.1
```

#### R3

```
ip route 1.1.1.0 255.255.255.0 23.1.1.2
ip route 2.2.2.0 255.255.255.0 23.1.1.2
ip route 12.1.1.0 255.255.255.0 23.1.1.2
```

---

### EIGRP Setting

#### R3

```
router eigrp 100
no auto-summary
network 23.1.1.3 0.0.0.0
network 34.1.1.3 0.0.0.0

- Redistribute Setting -
redistribute static
```

#### R4

```
router eigrp 100
no auto-summary
passive-interface g0/0
network 4.4.4.1 0.0.0.0
network 34.1.1.4 0.0.0.0
network 45.1.1.4 0.0.0.0
```

#### R5

```
router eigrp 100
no auto-summary
passive-interface g0/0
network 5.5.5.1 0.0.0.0
network 45.1.1.5 0.0.0.0
```

---

### PC에서의 통신 테스트

- PC1(1.1.1.11)  
![12](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/12.png)

- PC2(2.2.2.11)  
![13](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/13.png)

- PC3(4.4.4.11)  
![14](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/14.png)

- PC4(5.5.5.11)  
![15](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/15.png)

---
