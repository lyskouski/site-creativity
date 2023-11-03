<?php namespace Defines;

/**
 * Define all PDO types
 *
 * @sample
 *      echo $this->partial('Basic/Nav/catalog', array(
 *          'list' => \Defines\Catalog::getOeuvre(),
 *          'url' => 'oeuvre/search',
 *          'url_active' => (new \Engine\Request\Input)->getGet('/0')
 *      ));
 *
 * @author Viachaslau Lyskouski
 * @since 2015-11-18
 * @package Defines
 */
class Catalog
{

    protected static function updateLanguage($lang)
    {
        $oTranslate = \System\Registry::translation();
        $old = $oTranslate->getTargetLanguage();
        if (!is_null($lang) && $old !== $lang) {
            $oTranslate->setTargetLanguage($lang);
        } else {
            $old = null;
        }
        return $old;
    }

    /**
     * Get Oeuvre navigation list
     *
     * @return array
     */
    public static function getOeuvre($lang = null)
    {
        $oTranslate = \System\Registry::translation();
        $old = self::updateLanguage($lang);
        // 'title' => $oTranslate->sys('LB_CATEGORY_ОEUVRE')
        $result = array(
            ['title' => $oTranslate->sys('LB_CATEGORY_PROSE'),
                'sub' => array(
                    ['title' => $oTranslate->sys('LB_CATEGORY_ACTION')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_ALTERNATE_PROSE')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_DETECTIVE')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_PARABLE')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_FANTASY'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_FANTASIE')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_LOST_WORLDS'),
                                'sub' => array(
                                    ['title' => $oTranslate->sys('LB_CATEGORY_ALIDERIYA')],
                                    ['title' => $oTranslate->sys('LB_CATEGORY_ISHTWAR')],
                                    ['title' => $oTranslate->sys('LB_CATEGORY_KLANZ')],
                                    ['title' => $oTranslate->sys('LB_CATEGORY_LEAGUE_OF_HEROES')],
                                    ['title' => $oTranslate->sys('LB_CATEGORY_THE_EDGE_OF_REALITY')],
                                )],
                            ['title' => $oTranslate->sys('LB_CATEGORY_SCIENCE_FICTION')],
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_MYSTIC')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_NOVEL')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_POSTMODERNISM')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_PSYCHEDELIC')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_REALISM')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_SURREALISM')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_THRILLER')],
                )],
            ['title' => $oTranslate->sys('LB_CATEGORY_POETRY'),
                'sub' => array(
                    ['title' => $oTranslate->sys('LB_CATEGORY_ALTERNATIVE_POETRY')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_COLLECTION_OF_POEMS')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_VERSE'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_ACRO')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_BLANK_VERSE')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_CHARADE')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_FREE_VERSE')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_IN_PROSE')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_MAZE')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_MONORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_SONNET')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_VERS_LIBRE')],
                        )]
                )]
        );
        $old && $oTranslate->setTargetLanguage($old);
        return $result;
    }

    /**
     * Get Mind navigation list
     *
     * @return array
     */
    public static function getMind($lang = null)
    {
        $oTranslate = \System\Registry::translation();
        $old = self::updateLanguage($lang);
        // 'title' => $oTranslate->sys('LB_CATEGORY_MNEMONICS')
        $result = array(
            array(
                'title' => $oTranslate->sys('LB_CATEGORY_METHODS'),
                'sub' => array(
                    ['title' => $oTranslate->sys('LB_CATEGORY_ACTIVATION_OF_CREATIVITY')],
                    ['title' => $oTranslate->sys('LB_CATEGORY_SENSORY_MODALITY'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_VISUAL_MEMORY')],
        //                    ['title' => $oTranslate->sys('LB_CATEGORY_KINESTHETIC_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_AURAL_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_FLAVORING_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_OLFACTORY_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_TACTILE_MEMORY')],
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_IN_CONTENT'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_FIGURATIVE_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_MOTOR_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_EMOTIONAL_MEMORY')]
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_MEMORY_ORGANIZATION'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_EPISODIC_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_SEMANTIC_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_PROCEDURAL_MEMORY')]
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_TIME_CHARACTERISTICS'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_LONGTERM_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_SHORTTERM_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_ULTRASHORTTERM_MEMORY')]
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_TARGET_DESIGNATION'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_ARBITRARY_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_INVOLUNTARY_MEMORY')]
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_AVAILABILITY_OF_MEANS'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_INDIRECT_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_DIRECT_MEMORY')]
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_STORING_METHOD'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_INTERNAL_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_EXTERNAL_MEMORY')]
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_SYMBOLIC_MEMORY'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_LOGICAL_MEMORY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_VERBAL_MEMORY')],
                        )]
                )
            ),
            array(
                'title' => $oTranslate->sys('LB_CATEGORY_OBJECTS'),
                'sub' => array(
                    ['title' => $oTranslate->sys('LB_CATEGORY_EXACT_SCIENCE'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_CHEMISTRY')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_MATHEMATICS')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_PHYSICS')],
                            ['title' => $oTranslate->sys('LB_CATEGORY_SEMIOLOGY')],
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_SOCIAL_SCIENCIES'),// Социальные науки
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_LINGUISTICS'),
                                'sub' => array(
                                    ['title' => $oTranslate->sys('LB_CATEGORY_BELARUSIAN')],
                                    ['title' => $oTranslate->sys('LB_CATEGORY_ENGLISH')],
                                )]
                            // Психология
                            // География
                            // Культурология
                            // Педагогика
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_HUMANITIES'), // Гуманитарные науки
                        'sub' => array(
                            // философия;
                            // литература;
                            ['title' => $oTranslate->sys('LB_CATEGORY_ART_HISTORY')] // искусствоведение;
                        )],
                    ['title' => $oTranslate->sys('LB_CATEGORY_MUSIC_EDUCATION'),
                        'sub' => array(
                            ['title' => $oTranslate->sys('LB_CATEGORY_GUITAR')],
                        )]
                )
            )
        );

        $old && $oTranslate->setTargetLanguage($old);
        return $result;
    }
}
