<?php

class Susceptible
{
	public function index($content)
	{
		$Model = new Model();
		$sql = 'CREATE TABLE IF NOT EXISTS `' . C('DB_PREFIX') . 'susceptible` (`id` int(11) NOT NULL AUTO_INCREMENT,`word` varchar(100) NOT NULL,`state` int(11) NOT NULL DEFAULT \'0\',`addtime` int(11) NOT NULL DEFAULT \'0\',`time` int(11) NOT NULL DEFAULT \'0\',PRIMARY KEY (`id`),KEY `word` (`word`,`state`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
		@mysql_query($sql);
		$Susceptible = M('Susceptible')->find();

		if ($Susceptible == '') {
			$words = '阿扁推翻|1阿宾|1阿賓|1挨了一炮|1爱液横流|1安街逆|1安局办公楼|1安局豪华|1安门事|1安眠藥|1案的准确|1八九民|1八九学|1八九政治|1把病人整|1把邓小平|1把学生整|1罢工门|1白黄牙签|1败培训|1办本科|1办理本科|1办理各种|1办理票据|1办理文凭|1办理真实|1办理证书|1办理资格|1办文凭|1办怔|1办证|1半刺刀|1辦毕业|1辦證|1谤罪获刑|1磅解码器|1磅遥控器|1宝在甘肃修|1保过答案|1报复执法|1爆发骚|1北省委门|1被打死|1被指抄袭|1被中共|1本公司担|1本无码|1毕业證|1变牌绝|1辩词与梦|1冰毒|1冰火毒|1冰火佳|1冰火九重|1冰火漫|1冰淫传|1冰在火上|1波推龙|1博彩娱|1博会暂停|1博园区伪|1不查都|1不查全|1不思四化|1布卖淫女|1部忙组阁|1部是这样|1才知道只生|1财众科技|1采花堂|1踩踏事|1苍山兰|1苍蝇水|1藏春阁|1藏獨|1操了嫂|1操嫂子|1策没有不|1插屁屁|1察象蚂|1拆迁灭|1车牌隐|1成人电|1成人卡通|1成人聊|1成人片|1成人视|1成人图|1成人文|1成人小|1城管灭|1惩公安|1惩贪难|1充气娃|1冲凉死|1抽着大中|1抽着芙蓉|1出成绩付|1出售发票|1出售军|1穿透仪器|1春水横溢|1纯度白|1纯度黄|1次通过考|1催眠水|1催情粉|1催情药|1催情藥|1挫仑|1达毕业证|1答案包|1答案提供|1打标语|1打错门|1打飞机专|1打死经过|1打死人|1打砸办公|1大鸡巴|1大雞巴|1大纪元|1大揭露|1大奶子|1大批贪官|1大肉棒|1大嘴歌|1代办发票|1代办各|1代办文|1代办学|1代办制|1代辦|1代表烦|1代開|1代考|1代理发票|1代理票据|1代您考|1代您考|1代写毕|1代写论|1代孕|1贷办|1贷借款|1贷开|1戴海静|1当代七整|1当官要精|1当官在于|1党的官|1党后萎|1党前干劲|1刀架保安|1导的情人|1导叫失|1导人的最|1导人最|1导小商|1到花心|1得财兼|1的同修|1灯草和|1等级證|1等屁民|1等人老百|1等人是老|1等人手术|1邓爷爷转|1邓玉娇|1地产之歌|1地下先烈|1地震哥|1帝国之梦|1递纸死|1点数优惠|1电狗|1电话监|1电鸡|1甸果敢|1蝶舞按|1丁香社|1丁子霖|1顶花心|1东北独立|1东复活|1东京热|1東京熱|1洞小口紧|1都当警|1都当小姐|1都进中央|1毒蛇钻|1独立台湾|1赌球网|1短信截|1对日强硬|1多美康|1躲猫猫|1俄羅斯|1恶势力操|1恶势力插|1恩氟烷|1儿园惨|1儿园砍|1儿园杀|1儿园凶|1二奶大|1发牌绝|1发票出|1发票代|1发票销|1發票|1法车仑|1法伦功|1法轮|1法轮佛|1法维权|1法一轮|1法院给废|1法正乾|1反测速雷|1反雷达测|1反屏蔽|1范燕琼|1方迷香|1防电子眼|1防身药水|1房贷给废|1仿真枪|1仿真证|1诽谤罪|1费私服|1封锁消|1佛同修|1夫妻交换|1福尔马林|1福娃的預|1福娃頭上|1福香巴|1府包庇|1府集中领|1妇销魂|1附送枪|1复印件生|1复印件制|1富民穷|1富婆给废|1改号软件|1感扑克|1冈本真|1肛交|1肛门是邻|1岡本真|1钢针狗|1钢珠枪|1港澳博球|1港馬會|1港鑫華|1高就在政|1高考黑|1高莺莺|1搞媛交|1告长期|1告洋状|1格证考试|1各类考试|1各类文凭|1跟踪器|1工程吞得|1工力人|1公安错打|1公安网监|1公开小姐|1攻官小姐|1共狗|1共王储|1狗粮|1狗屁专家|1鼓动一些|1乖乖粉|1官商勾|1官也不容|1官因发帖|1光学真题|1跪真相|1滚圆大乳|1国际投注|1国家妓|1国家软弱|1国家吞得|1国库折|1国一九五七|1國內美|1哈药直销|1海访民|1豪圈钱|1号屏蔽器|1和狗交|1和狗性|1和狗做|1黑火药的|1红色恐怖|1红外透视|1紅色恐|1胡江内斗|1胡紧套|1胡錦濤|1胡适眼|1胡耀邦|1湖淫娘|1虎头猎|1华国锋|1华门开|1化学扫盲|1划老公|1还会吹萧|1还看锦涛|1环球证件|1换妻|1皇冠投注|1黄冰|1浑圆豪乳|1活不起|1火车也疯|1机定位器|1机号定|1机号卫|1机卡密|1机屏蔽器|1基本靠吼|1绩过后付|1激情电|1激情短|1激情妹|1激情炮|1级办理|1级答案|1急需嫖|1集体打砸|1集体腐|1挤乳汁|1擠乳汁|1佳静安定|1家一样饱|1家属被打|1甲虫跳|1甲流了|1奸成瘾|1兼职上门|1监听器|1监听王|1简易炸|1江胡内斗|1江太上|1江系人|1江贼民|1疆獨|1蒋彦永|1叫自慰|1揭贪难|1姐包夜|1姐服务|1姐兼职|1姐上门|1金扎金|1金钟气|1津大地震|1津地震|1进来的罪|1京地震|1京要地震|1经典谎言|1精子射在|1警察被|1警察的幌|1警察殴打|1警察说保|1警车雷达|1警方包庇|1警用品|1径步枪|1敬请忍|1究生答案|1九龙论坛|1九评共|1酒象喝汤|1酒像喝汤|1就爱插|1就要色|1举国体|1巨乳|1据说全民|1绝食声|1军长发威|1军刺|1军品特|1军用手|1开邓选|1开锁工具|1開碼|1開票|1砍杀幼|1砍伤儿|1康没有不|1康跳楼|1考答案|1考后付款|1考机构|1考考邓|1考联盟|1考前答|1考前答案|1考前付|1考设备|1考试包过|1考试保|1考试答案|1考试机构|1考试联盟|1考试枪|1考研考中|1考中答案|1磕彰|1克分析|1克千术|1克透视|1空和雅典|1孔摄像|1控诉世博|1控制媒|1口手枪|1骷髅死|1快速办|1矿难不公|1拉登说|1拉开水晶|1来福猎|1拦截器|1狼全部跪|1浪穴|1老虎机|1雷人女官|1类准确答|1黎阳平|1李洪志|1李咏曰|1理各种证|1理是影帝|1理证件|1理做帐报|1力骗中央|1力月西|1丽媛离|1利他林|1连发手|1聯繫電|1炼大法|1两岸才子|1两会代|1两会又三|1聊视频|1聊斋艳|1了件渔袍|1猎好帮手|1猎枪销|1猎槍|1獵槍|1领土拿|1流血事|1六合彩|1六死|1六四事|1六月联盟|1龙湾事件|1隆手指|1陆封锁|1陆同修|1氯胺酮|1乱奸|1乱伦类|1乱伦小|1亂倫|1伦理大|1伦理电影|1伦理毛|1伦理片|1轮功|1轮手枪|1论文代|1罗斯小姐|1裸聊网|1裸舞视|1落霞缀|1麻古|1麻果配|1麻果丸|1麻将透|1麻醉狗|1麻醉枪|1麻醉槍|1麻醉藥|1蟆叫专家|1卖地财政|1卖发票|1卖银行卡|1卖自考|1漫步丝|1忙爱国|1猫眼工具|1毛一鲜|1媒体封锁|1每周一死|1美艳少妇|1妹按摩|1妹上门|1门按摩|1门保健|1門服務|1氓培训|1蒙汗药|1迷幻型|1迷幻药|1迷幻藥|1迷昏口|1迷昏药|1迷昏藥|1迷魂香|1迷魂药|1迷魂藥|1迷奸药|1迷情水|1迷情药|1迷藥|1谜奸药|1蜜穴|1灭绝罪|1民储害|1民九亿商|1民抗议|1明慧网|1铭记印尼|1摩小姐|1母乳家|1木齐针|1幕没有不|1幕前戲|1内射|1南充针|1嫩穴|1嫩阴|1泥马之歌|1你的西域|1拟涛哥|1娘两腿之间|1妞上门|1浓精|1怒的志愿|1女被人家搞|1女激情|1女技师|1女人和狗|1女任职名|1女上门|1女優|1鸥之歌|1拍肩神药|1拍肩型|1牌分析|1牌技网|1炮的小蜜|1陪考枪|1配有消|1喷尿|1嫖俄罗|1嫖鸡|1平惨案|1平叫到床|1仆不怕饮|1普通嘌|1期货配|1奇迹的黄|1奇淫散|1骑单车出|1气狗|1气枪|1汽狗|1汽枪|1氣槍|1铅弹|1钱三字经|1枪出售|1枪的参|1枪的分|1枪的结|1枪的制|1枪货到|1枪决女犯|1枪决现场|1枪模|1枪手队|1枪手网|1枪销售|1枪械制|1枪子弹|1强权政府|1强硬发言|1抢其火炬|1切听器|1窃听器|1禽流感了|1勤捞致|1氢弹手|1清除负面|1清純壆|1情聊天室|1情妹妹|1情视频|1情自拍|1氰化钾|1氰化钠|1请集会|1请示威|1请愿|1琼花问|1区的雷人|1娶韩国|1全真证|1群奸暴|1群起抗暴|1群体性事|1绕过封锁|1惹的国|1人权律|1人体艺|1人游行|1人在云上|1人真钱|1认牌绝|1任于斯国|1柔胸粉|1肉洞|1肉棍|1如厕死|1乳交|1软弱的国|1赛后骚|1三挫|1三级片|1三秒倒|1三网友|1三唑|1骚妇|1骚浪|1骚穴|1骚嘴|1扫了爷爷|1色电影|1色妹妹|1色视频|1色小说|1杀指南|1山涉黑|1煽动不明|1煽动群众|1上门激|1烧公安局|1烧瓶的|1韶关斗|1韶关玩|1韶关旭|1射网枪|1涉嫌抄袭|1深喉冰|1神七假|1神韵艺术|1生被砍|1生踩踏|1生肖中特|1圣战不息|1盛行在舞|1尸博|1失身水|1失意药|1狮子旗|1十八等|1十大谎|1十大禁|1十个预言|1十类人不|1十七大幕|1实毕业证|1实体娃|1实学历文|1士康事件|1式粉推|1视解密|1是躲猫|1手变牌|1手答案|1手狗|1手机跟|1手机监|1手机窃|1手机追|1手拉鸡|1手木仓|1手槍|1守所死法|1兽交|1售步枪|1售纯度|1售单管|1售弹簧刀|1售防身|1售狗子|1售虎头|1售火药|1售假币|1售健卫|1售军用|1售猎枪|1售氯胺|1售麻醉|1售冒名|1售枪支|1售热武|1售三棱|1售手枪|1售五四|1售信用|1售一元硬|1售子弹|1售左轮|1书办理|1熟妇|1术牌具|1双管立|1双管平|1水阎王|1丝护士|1丝情侣|1丝袜保|1丝袜恋|1丝袜美|1丝袜妹|1丝袜网|1丝足按|1司长期有|1司法黑|1私房写真|1死法分布|1死要见毛|1四博会|1四大扯|1个四小码|1苏家屯集|1诉讼集团|1素女心|1速代办|1速取证|1酸羟亚胺|1蹋纳税|1太王四神|1泰兴幼|1泰兴镇中|1泰州幼|1贪官也辛|1探测狗|1涛共产|1涛一样胡|1特工资|1特码|1特上门|1体透视镜|1替考|1替人体|1天朝特|1天鹅之旅|1天推广歌|1田罢工|1田田桑|1田停工|1庭保养|1庭审直播|1通钢总经|1偷電器|1偷肃贪|1偷听器|1偷偷贪|1头双管|1透视功能|1透视镜|1透视扑|1透视器|1透视眼镜|1透视药|1透视仪|1秃鹰汽|1突破封锁|1突破网路|1推油按|1脱衣艳|1瓦斯手|1袜按摩|1外透视镜|1外围赌球|1湾版假|1万能钥匙|1万人骚动|1王立军|1王益案|1网民案|1网民获刑|1网民诬|1微型摄像|1围攻警|1围攻上海|1维汉员|1维权基|1维权人|1维权谈|1委坐船|1谓的和谐|1温家堡|1温切斯特|1温影帝|1溫家寶|1瘟加饱|1瘟假饱|1文凭证|1文强|1纹了毛|1闻被控制|1闻封锁|1瓮安|1我的西域|1我搞台独|1乌蝇水|1无耻语录|1无码专|1五套功|1五月天|1午夜电|1午夜极|1武警暴|1武警殴|1武警已增|1务员答案|1务员考试|1雾型迷|1西藏限|1西服进去|1希脏|1习进平|1习晋平|1席复活|1席临终前|1席指着护|1洗澡死|1喜贪赃|1先烈纷纷|1现大地震|1现金投注|1线透视镜|1限制言|1陷害案|1陷害罪|1相自首|1香港论坛|1香港马会|1香港一类|1香港总彩|1硝化甘|1小穴|1校骚乱|1协晃悠|1写两会|1泄漏的内|1新建户|1新疆叛|1新疆限|1新金瓶|1新唐人|1信访专班|1信接收器|1兴中心幼|1星上门|1行长王益|1形透视镜|1型手枪|1姓忽悠|1幸运码|1性爱日|1性福情|1性感少|1性推广歌|1胸主席|1徐玉元|1学骚乱|1学位證|1學生妹|1丫与王益|1烟感器|1严晓玲|1言被劳教|1言论罪|1盐酸曲|1颜射|1恙虫病|1姚明进去|1要人权|1要射精了|1要射了|1要泄了|1夜激情|1液体炸|1一小撮别|1遗情书|1蚁力神|1益关注组|1益受贿|1阴间来电|1陰唇|1陰道|1陰戶|1淫魔舞|1淫情女|1淫肉|1淫騷妹|1淫兽|1淫兽学|1淫水|1淫穴|1隐形耳|1隐形喷剂|1应子弹|1婴儿命|1咏妓|1用手枪|1幽谷三|1游精佑|1有奶不一|1右转是政|1幼齿类|1娱乐透视|1愚民同|1愚民政|1与狗性|1玉蒲团|1育部女官|1冤民大|1鸳鸯洗|1园惨案|1园发生砍|1园砍杀|1园凶杀|1园血案|1原一九五七|1原装弹|1袁腾飞|1晕倒型|1韵徐娘|1遭便衣|1遭到警|1遭警察|1遭武警|1择油录|1曾道人|1炸弹教|1炸弹遥控|1炸广州|1炸立交|1炸药的制|1炸药配|1炸药制|1张春桥|1找枪手|1找援交|1找政法委副|1赵紫阳|1针刺案|1针刺伤|1针刺事|1针刺死|1侦探设备|1真钱斗地|1真钱投注真善忍|1真实文凭|1真实资格|1震惊一个民|1震其国土|1证到付款|1证件办|1证件集团|1证生成器|1证书办|1证一次性|1政府操|1政论区|1證件|1植物冰|1殖器护|1指纹考勤|1指纹膜|1指纹套|1至国家高|1志不愿跟|1制服诱|1制手枪|1制证定金|1制作证件|1中的班禅|1中共黑|1中国不强|1种公务员|1种学历证|1众像羔|1州惨案|1州大批贪|1州三箭|1宙最高法|1昼将近|1主席忏|1住英国房|1助考|1助考网|1专业办理|1专业代|1专业代写|1专业助|1转是政府|1赚钱资料|1装弹甲|1装枪套|1装消音|1着护士的胸|1着涛哥|1姿不对死|1资格證|1资料泄|1梓健特药|1字牌汽|1自己找枪|1自慰用|1自由圣|1自由亚|1总会美女|1足球玩法|1最牛公安|1醉钢枪|1醉迷药|1醉乙醚|1尊爵粉|1左转是政|1作弊器|1作各种证|1作硝化甘|1唑仑|1做爱小|1做原子弹|1做证件';
			$wordsarray = explode('|1', $words);

			foreach ($wordsarray as $wo) {
				$Susceptible_id = M('Susceptible')->add(array('word' => $wo, 'addtime' => time(), 'time' => time()));
			}
		}

		if (MODULE_NAME != 'Susceptible') {
			$Susceptible_S = S('Susceptible' . C('site_url'));

			if ($Susceptible_S == '') {
				$Susceptible_S = M('Susceptible')->where(array('state' => 1))->field('word')->select();
				S('Susceptible' . C('site_url'), $Susceptible_S);
			}

			$Susceptible = $Susceptible_S;

			if ($Susceptible != '') {
				foreach ($Susceptible as $so) {
					$num = mb_strlen($so['word'], 'utf8');
					$xing = '';

					for ($i = 0; $i < $num; $i++) {
						$xing .= '*';
					}

					$xing_array[] = $xing;
					$word_array[] = $so['word'];
				}
			}

			$content = str_replace($word_array, $xing_array, $content);
		}

		return $content;
	}
}


?>
