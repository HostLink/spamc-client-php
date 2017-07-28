<?php

require '..\Client.php';
require '..\Response.php';

use Winco\Antispam\Spamc\Client;
use Winco\Antispam\Spamc\Response;

$client = new Client;

$clean = <<<EOT
Delivered-To: luiz.inacio.correa@gmail.com
Received: by 10.140.93.97 with SMTP id c88csp1915014qge;
        Thu, 27 Jul 2017 07:48:17 -0700 (PDT)
X-Received: by 10.107.6.78 with SMTP id 75mr5659176iog.136.1501166897280;
        Thu, 27 Jul 2017 07:48:17 -0700 (PDT)
ARC-Seal: i=1; a=rsa-sha256; t=1501166897; cv=none;
        d=google.com; s=arc-20160816;
        b=n/vFACKsSA8YFPBdHgvGDRc0o1gif0PHjnVhdUNCKgTf4fFb2kBWC+WALztHz4Rs3i
         WbgJz5B5ZmKHLIS482lmGqh3Tn0WqVhMKJlpQeWzZgqPSh5FFq1dfduIpEYShwJYiqpY
         V2IQ8LIgzABDNnC9VljEN1ZSN8GOhciLypHOjSqG2E/ORmDpLCsn1J+vPCca0FkU/h6R
         8X0bRPxieU1QsduflZGFCfNtI8J3cvOtqzFeFwtvjzL3jmrS9/1gHm19J1bjBUvn8I8k
         iiHHQEHeg+vfscslPSD5A3vXW8SbPjvF9B5SvOYs+jdnGIHmFbAK3K+h+wGfq+Nh+iYc
         zwug==
ARC-Message-Signature: i=1; a=rsa-sha256; c=relaxed/relaxed; d=google.com; s=arc-20160816;
        h=message-id:list-unsubscribe:sender:content-transfer-encoding
         :mime-version:date:subject:reply-to:from:to:domainkey-signature
         :dkim-signature:arc-authentication-results;
        bh=iRSwc9dUOaCrb4Z79ZSQa8n654Fn9/IOL039rh8UFLo=;
        b=i2bgmmYCajKE0QpO7fEr93vjGpsXlMTI37JiIUWTEcahYYCdccq1MzavHLakUepdEX
         RcrLk22wJfx0bocytgqrW0hy8OiJhfiKxMnA372AZ1+k+IYC9Ythyvyq6SElcX1hhNUp
         5UoDBiTt4bSQB+8MlO/hQsWXdE54RHuxp1TnmPyT+tTifRhloZJfxQO5qce25Kgq301I
         dcnBvnlVTD6PDWsLCdDniS6/gJ5Bjyimzk6STeTCW8SM6u068vUYaxk2uNFuKztzZR4S
         +hAsYdSjsky0gXkgUunMZfmFf1NDXZhHeUDqbmRwCxNx2zIiY38nPNzz3JxhaN77yo8r
         eaYA==
ARC-Authentication-Results: i=1; mx.google.com;
       dkim=pass header.i=@s4.acemsrvg.com header.s=dk header.b=mAbMrCf+;
       spf=pass (google.com: domain of bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com designates 192.92.97.238 as permitted sender) smtp.mailfrom=bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com
Return-Path: <bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com>
Received: from s4.acemsrvg.com (s4.acemsrvg.com. [192.92.97.238])
        by mx.google.com with ESMTPS id c11si15077788ith.200.2017.07.27.07.48.17
        for <luiz.inacio.correa@gmail.com>
        (version=TLS1_2 cipher=ECDHE-RSA-AES128-GCM-SHA256 bits=128/128);
        Thu, 27 Jul 2017 07:48:17 -0700 (PDT)
Received-SPF: pass (google.com: domain of bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com designates 192.92.97.238 as permitted sender) client-ip=192.92.97.238;
Authentication-Results: mx.google.com;
       dkim=pass header.i=@s4.acemsrvg.com header.s=dk header.b=mAbMrCf+;
       spf=pass (google.com: domain of bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com designates 192.92.97.238 as permitted sender) smtp.mailfrom=bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com
DKIM-Signature: v=1; a=rsa-sha1; c=relaxed/relaxed; s=dk; d=s4.acemsrvg.com; h=To:From:Reply-To:Subject:Date:MIME-Version:Content-Type:Content-Transfer-Encoding:Sender:List-Unsubscribe:Message-ID; i=vanhack92327.activehosted.com@s4.acemsrvg.com; bh=dPFS8Cp3c2gF+M2O8iMfMmDzXII=; b=mAbMrCf+5c2Jl7ufGHyAaZsBP/DyOL2IZ75mDjT1hugb6y/6SdX2vFz6ir4L8lF3joskT/wIvWAn
   pa5zvEECt2ccNWIyYj6QTo1iLtD7qWGgnWUfkl7t95qGbLA2RS29v+knBjR3weA4ubWaSU/8jYgk
   2mTj/fSY9Czr1MXUDoY=
DomainKey-Signature: a=rsa-sha1; c=nofws; q=dns; s=dk; d=s4.acemsrvg.com; b=WyG/jS4O/NskM9nxbk9vpVffkXMPoJljEr/1sRFKzJWYoSy4dmxI15eT7VdA6i2umlikxvSn19NZ
   9E+syFO2aQQAzkHtKbF6IJgYsQYGMNZ9R0t9lJs6AvHn3egEETDttrS+AVwxVeHIa1oeiBLcLY3q
   nrDRzlXzwvDWk6UMn1s=;
Received: by s4.acemsrvg.com id hf7uj225nf81 for <luiz.inacio.correa@gmail.com>; Thu, 27 Jul 2017 09:24:15 -0500 (envelope-from <bounce-411654-17-29016-luiz.inacio.correa=gmail.com@s4.acemsrvg.com>)
To: "Luiz Inácio" <luiz.inacio.correa@gmail.com>
From: Ilya from VanHack <ilya@vanhack.com>
Reply-To: contact@vanhack.com
Subject: Get a Visa by Starting a Business in Canada
Date: Thu, 27 Jul 2017 09:03:19 -0500
MIME-Version: 1.0
Content-Type: multipart/alternative; boundary="_=_swift-17794997585979f2a763bdf0.71802406_=_"
Content-Transfer-Encoding: 7bit
Sender: <vanhack92327.activehosted.com@s4.acemsrvg.com>
X-Sender: <vanhack92327.activehosted.com@s4.acemsrvg.com>
X-Report-Abuse: Please report abuse here: http://www.activecampaign.com/contact/?type=abuse
X-mid: bHVpei5pbmFjaW8uY29ycmVhQGdtYWlsLmNvbSAsIGMxNyAsIG0zNCAsIHM1MjQ0
List-Unsubscribe: <mailto:unsubscribe-17-63dadd3f1e54c9941342f66a5a03e66b@vanhack92327.activehosted.com>, <http://vanhack92327.acemlnc.com/box.php?nl=1&c=17&m=34&s=63dadd3f1e54c9941342f66a5a03e66b&funcml=unsub2&luha=1>
Message-ID: <20170727142414.1003.1376757174.swift@vanhack92327.activehosted.com>
EOT;

print_r($client->check($clean));


$spam = <<<EOT
Delivered-To: luiz.inacio.correa@gmail.com
Received: by 10.140.93.97 with SMTP id c88csp2375887qge;
        Thu, 27 Jul 2017 16:45:53 -0700 (PDT)
X-Received: by 10.37.124.65 with SMTP id x62mr5215282ybc.41.1501199153901;
        Thu, 27 Jul 2017 16:45:53 -0700 (PDT)
ARC-Seal: i=1; a=rsa-sha256; t=1501199153; cv=none;
        d=google.com; s=arc-20160816;
        b=sm+4apq5zuOS+N2xKqjNky0uao76XTjobcwhIw0LfASsIHhDFgzTSdFiRylmNHWVt3
         EVLdIXmsuHirAuGqLteLIEUCsSw8dtOZZKYKjdl5U5juU4E4eKX+wkLJXZYsuHpI9efV
         3pgsSKI2xrmu4WKVLCJDC8KOXpYq2am1eaml2KmyzjLRa2B9UoF390uXMMgMKIqLmmNi
         Ac5OsY/dkxjj9odg53tQaw0aQwC3qBHZj0zz58e7O0bzQ3Guy+5AdvpyKMg5IJLjZfei
         cQCHnClrDQwW3VnTHx+LKZwoDwqwLt3p2/TfV2NvbRsPP6J61jfB09x5jBqvNM/JD40A
         4GRw==
ARC-Message-Signature: i=1; a=rsa-sha256; c=relaxed/relaxed; d=google.com; s=arc-20160816;
        h=content-transfer-encoding:list-unsubscribe:mime-version:reply-to
         :from:date:message-id:subject:to:dkim-signature
         :arc-authentication-results;
        bh=VGTm5FNjNRDXrZtsXpxrk0K/yj50vnaeqmoMrzUGfFU=;
        b=OcDzKuLyCH7JHx6ePAcLo7MYvWE9EcPeho+OZNh4CHPF/vRzNZoOFrV8gXLxXR4g1E
         NVe2M7O+wloHRYPm/vd8RILJ8ZnrFL8AA6yHu2HUJwyBI/0qnFHJ/FDyEpRYwGgkrVKc
         d84GZWP7p7Xuvruy2lNMwKTgzsLoRdswXDSWnnDZle+BJYJGwB6pLbgVdyqC3D+5Qzus
         m2WhTilr81XXx6z4XqTk4LWvqCxE5MtxmiZfN/e8TKtrlEEVmsx3R4WKf7PtJ91XbMpt
         PnYkmiT+qUnn4JHjM4vh9WdixNpM4e2Lc7R71ISabnBMZxIwyGG0tpM9VthCLStoj+My
         atAA==
ARC-Authentication-Results: i=1; mx.google.com;
       dkim=pass header.i=@supermoda5.com.br header.b=J3GvK1z6;
       spf=pass (google.com: domain of return@supermoda5.com.br designates 208.234.29.196 as permitted sender) smtp.mailfrom=return@supermoda5.com.br;
       dmarc=pass (p=NONE sp=NONE dis=NONE) header.from=supermoda5.com.br
Return-Path: <return@supermoda5.com.br>
Received: from jorns2.supermoda5.com.br (jorns2.supermoda5.com.br. [208.234.29.196])
        by mx.google.com with ESMTP id r17si2097577ywc.421.2017.07.27.16.45.53
        for <luiz.inacio.correa@gmail.com>;
        Thu, 27 Jul 2017 16:45:53 -0700 (PDT)
Received-SPF: pass (google.com: domain of return@supermoda5.com.br designates 208.234.29.196 as permitted sender) client-ip=208.234.29.196;
Authentication-Results: mx.google.com;
       dkim=pass header.i=@supermoda5.com.br header.b=J3GvK1z6;
       spf=pass (google.com: domain of return@supermoda5.com.br designates 208.234.29.196 as permitted sender) smtp.mailfrom=return@supermoda5.com.br;
       dmarc=pass (p=NONE sp=NONE dis=NONE) header.from=supermoda5.com.br
DKIM-Signature: v=1; a=rsa-sha1; c=relaxed/relaxed; s=default; d=supermoda5.com.br; h=To:Subject:Message-ID:Date:From:Reply-To:MIME-Version:List-Unsubscribe:Content-Type:Content-Transfer-Encoding; i=abuse@supermoda5.com.br; bh=Qknkayegd+/qSv2qtpoEEpl0NUs=; b=J3GvK1z6ZyjUU0swFbieck0dlZ1Ex/QhMFFurmG0jlp8GU7kAJ+LpH/xSGlyJkvaDE58MXjZ0A/6
   ixqFnMj7ZALzaPShWT/YBjR83I0dYvHW2sAFCrgmHq88grfYPdoAR9h2vfhC4kArDA+xsOHuwCLm
   +WteIAsuNtmSSkaoAP0=
To: luiz.inacio.correa@gmail.com
Subject: Vinhos chilenos e franceses com até 55%OFF no Festival Grandes Regiões da Evino
Message-ID: <8255f99c98ceb0d98d2512640b5264c1@supermoda5.com.br>
Return-Path: return@supermoda5.com.br
Date: Thu, 27 Jul 2017 16:31:02 -0300
From: Especial Vinhos <claudia@supermoda5.com.br>
Reply-To: claudia@supermoda5.com.br
MIME-Version: 1.0
X-Mailer-LID: 2
List-Unsubscribe: <http://supermoda5.com.br/turbo/unsubscribe.php?M=399895&C=c333993356aa38d669942829a53aa64a&L=2&N=181>
X-Mailer-RecptId: 399895
X-Mailer-SID: 181
X-Mailer-Sent-By: 2
Content-Type: multipart/alternative; charset="UTF-8"; boundary="b1_c120878a2a31ad52f49038adc49b4271"
Content-Transfer-Encoding: 8bit
EOT;

print_r($client->check($spam));