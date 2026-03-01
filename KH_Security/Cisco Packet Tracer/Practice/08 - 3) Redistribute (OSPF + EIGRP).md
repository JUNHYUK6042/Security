# Cisco Router Redistribution

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

## OSPF + EIGRP 실습

- Interface 설정 및 라우터 기본 설정은 생략하겠습니다.
- Default Gateway는 static이기 때문에 3개의 프로토콜이 연결된 것입니다.
- classless 정보를 전송하기 위해서 사용된다

### OSPF + EIGRP 구성도

![26](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/26.png)

---

###  OSPF Setting

#### R1

```
router ospf 1
passive-interface g0/0
network 1.1.1.1 0.0.0.0 area 0
network 12.1.1.1 0.0.0.0 area 0
```

#### R2

```
router ospf 1
passive-interface g0/0
network 2.2.2.1 0.0.0.0 area 0
network 12.1.1.2 0.0.0.0 area 0
network 23.1.1.2 0.0.0.0 area 0
```

#### R3

```
router ospf 1
network 23.1.1.3 0.0.0.0 area 0
network 34.1.1.3 0.0.0.0 area 0

- Redistribute Setting -
redistribute eigrp 100 metric 10 subnets
default-information originate
```

---

###  EIGRP Setting

#### R3

```
router eigrp 100
no auto-summary
network 23.1.1.3 0.0.0.0
network 34.1.1.3 0.0.0.0

- Redistribute Setting -
redistribute ospf 1 metric 10000 1000 255 1 1500
```

#### R4

```
router eigrp 100
passive-interface g0/0
no auto-summary
network 4.4.4.1 0.0.0.0
network 34.1.1.4 0.0.0.0
network 45.1.1.4 0.0.0.0
```

#### R5

```
router eigrp 100
passive-interface g0/0
no aute-summary
network 5.5.5.1 0.0.0.0
network 45.1.1.5 0.0.0.0
```

---

### PC에서의 통신 테스트

- 위에 `RIP + OSPF`와 같이 통신이 잘 되는 것을 알 수 있습니다.

- PC1(1.1.1.11)  
![27](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/27.png)

- PC2(2.2.2.11)  
![28](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/28.png)

- PC3(4.4.4.11)  
![29](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/29.png)

- PC4(5.5.5.11)  
![30](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/30.png)

---
