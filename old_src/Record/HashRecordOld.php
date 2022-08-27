<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Hasher;

/**
 * Class HashRecordOld
 * @package EugeneErg\Preparer\RecordOld
 */
class HashRecordOld extends OldAbstractRecord
{
    /**
     * @var string;
     */
    private $hash;

    /**
     * @var self[]
     */
    private static $records = [];

    /** @inheritDoc */
    public function __construct()
    {
        parent::__construct();
        $this->hash = Hasher::getHash($this->getContainer());
        self::$records[$this->hash] = $this;
    }

    /**
     * @return string
     */
    protected function getStringValue(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return self
     */
    public static function findByHash(string $hash): ?self
    {
        return self::$records[$hash] ?? null;
    }

    /**
     * @inheritDoc
     */
    protected function getChildContainer(AbstractAction $action): Container
    {
        if ($action instanceof Property) {
            $hashRecord = self::findByHash($action->getName());

            if ($hashRecord) {

            }
        }

        return parent::getChildContainer($action);
    }
}

/**
 *
information_schema

old_wciom_ru

old_infographics_wciom_ru

wciom_ru_db_zh
    tr_zh_q_h
    tr_zh_q_v
    tr_zh_results
    tr_zh_survey
    tr_zh_var_h
    tr_zh_var_v
 *
    tr_zhp_q_h
    tr_zhp_q_v
    tr_zhp_results
    tr_zhp_survey
    tr_zhp_var_h
    tr_zhp_var_v
 *
    zh_q_h
    zh_q_v
    zh_results
    zh_survey
    zh_var_h
    zh_var_v
 *
    zhp_q_h
    zhp_q_v
    zhp_results
    zhp_survey
    zhp_var_h
    zhp_var_v


event_wciom_ru
    _grh_survey_answer
    _grh_survey_data
    _grh_survey_head
    _grh_survey_quest
    grh_foto_section
    grh_group
    grh_head_report
    grh_news
    grh_partner
        id
        image
        link
        marker
        type

grh_quest
    grh_section
    grh_section_detail
    grh_speakers
    grh_users
        hauth
        id
        login
        pd
        section
        type
 *
 *
1,petrenko@wciom.com,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,1,tr245477

2,nsedova,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,1,245477

3,sdavydov@hse.ru,Давыдов С.Г.,30,465315

4,fedorov@wciom.com,Федоров В.B.,1,762757

5,kuleshova@wciom.com,Кулешова А.В.,2,20,35,36,565611

6,aabes@inbox.ru ,Бесчасная А.А.,3,802377

7,uzubok@mail.ru,Зубок Ю.А.,4,320630

8,pautova@fom.ru,Паутова Л.А.,5,16,243675

9,ikozina@hse.ru,Козина И.М.,6,431119

10,zotova@zircon.ru,Зотова В.А.,9,712992

11,zadorin@zircon.ru,Задорин И.В.,10,24,365257

12,drobizheva@yandex.ru,Дробижева Л.М.,11,472176

14,silaslowa@mail.ru,Расходчиков А.Н.,12,788343

15,tanja_gta@mail.ru,Гужавина Т.А.,13,218024

16,roman_66@list.ru,Евстифеев Р.В.,14,637413

17,zvb@socio-fond.com,Звоновский В.Б.,15,225121

18,kildyushov@mail.ru ,Кильдюшов О.В.,17,781979

19,petrova@eaca.ru,Петрова Л.Е.,18,340731

20,aalmakaeva@hse.ru,Алмакаева А.М.,19,856149

21,mussel100@yandex.ru,Муссель М.К.,21,371887

22,a762rab@mail.ru,Александрова О.А.,22,753582

23,tarestova@gmail.com,Ильин Н.И.,23,815510

24,dobromelov@mail.ru,Добромелов Г.В.,24,137378

25,9166908616@mail.ru,Чернозуб О.Л.,25,334725

26,jbaskakova@gmail.com,Баскакова Ю.М.,26,27,28,294594

27,anna.andreenkova@cessi.ru,Андреенкова А.В.,29,273828

29,anosova@m-research.ru,Аносова А.,34,753582

30,antonpsevdonimov@mail.ru,Смолькин А.А.,8,234616

31,mv.ozerova@yandex.ru,Озерова М.,7,469462

58,lobanov,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,1,467952

59,kinyakin_a@wciom.com,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,234646

60,homutova_t@wciom.com,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,346546

61,savinskaya@gmail.com,Савинская О.Б.,3,966146

63,pavlenko.ks@gmail.com,Павленко К.,7,864131

65,mlgalas@fa.ru,Галас М.Л.,37,913193

66,shipova04@yandex.ru,Шипова Е.А.,12,736642

67,nelly@qualitas.ru,Романович Н.А.,15,659331

68,ermolenko@ifors.ru,Ермоленко Ю.,21,39,189232

69,t-ag2013@yandex.ru,Тюриков А.Г.,22,465235

70,am.demidov1951@gmail.com,Демидов А.M.,22,613356

71,natalia.ignatyeva@gfk.com,Игнатьева Н.,22,314967

72,gaa-mma@mail.ru ,Гребенюк А.А.,23,329530

73,vkiselev@we-change.ru,Киселев В.,25,664136

74,ooberemko@hse.ru,Оберемко О.А.,26,456523

75,makushevam@gmail.com,Макушева М.О.,27,498310

76,evgeny.popov@kantar.com,Попов Е.В.,34,964916



 *
 */
