<?php
/* @var $token string */
?>

<div class="phone-widget" style="margin-bottom: 30px">
	<?php if($token): ?>
    <div class="phone-widget__header">
        <div class="phone-widget__heading">
            <span class="phone-widget__title">Calls</span>
            <a href="#" class="phone-widget__close">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
                            fill="white" />
                </svg>
            </a>
        </div>
        <ul class="phone-widget__header-actions">
            <li>
                <a href="#" data-toggle-tab="tab-phone" class="is_active">
                    <i class="fas fa-phone"></i>
                    <span>Call</span>
                </a>
            </li>
            <li>
                <a href="#" data-toggle-tab="tab-contacts" >
                    <i class="far fa-address-book"></i>
                    <span>Contacts</span>
                </a>
            </li>
            <li>
                <a href="#" data-toggle-tab="tab-history">
                    <i class="fas fa-file-invoice"></i>
                    <span>history</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="phone-widget__body">
        <div class="phone-widget__tab is_active" id="tab-phone">
            <div class="call-pane">
                <div class="call-pane__number">
                    <div class="suggestion-placeholder">
                        <svg width="75" height="75" viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.97374 43.421C0.888156 43.421 0 44.3092 0 45.3946C0 46.4802 0.888156 47.3683 1.97374 47.3683C3.05933 47.3683 3.94749 46.4802 3.94749 45.3946C3.9473 44.3092 3.05914 43.421 1.97374 43.421Z" fill="white" fill-opacity="0.1"/>
                            <path d="M1.97374 27.6315C0.888156 27.6315 0 28.5196 0 29.6052C0 30.6908 0.888156 31.579 1.97374 31.579C3.05933 31.579 3.94749 30.6908 3.94749 29.6052C3.9473 28.5196 3.05914 27.6315 1.97374 27.6315Z" fill="white" fill-opacity="0.1"/>
                            <path d="M29.6053 71.0525C28.5197 71.0525 27.6315 71.9406 27.6315 73.0262C27.6315 74.1118 28.5197 75 29.6053 75C30.6909 75 31.579 74.1118 31.579 73.0262C31.579 71.9406 30.6909 71.0525 29.6053 71.0525Z" fill="white" fill-opacity="0.1"/>
                            <path d="M13.8159 9.86835C11.6449 9.86835 9.86859 11.6447 9.86859 13.8156C9.86859 15.9866 11.6447 17.7631 13.8159 17.7631C15.9871 17.7631 17.7632 15.9868 17.7632 13.8158C17.7632 11.6448 15.9869 9.86835 13.8159 9.86835Z" fill="white" fill-opacity="0.1"/>
                            <path d="M13.8159 25.6579C11.6449 25.6579 9.86859 27.4342 9.86859 29.6052C9.86859 31.7762 11.6449 33.5525 13.8159 33.5525C15.9869 33.5525 17.7632 31.7762 17.7632 29.6052C17.7632 27.4342 15.9869 25.6579 13.8159 25.6579Z" fill="white" fill-opacity="0.1"/>
                            <path d="M13.8159 41.4473C11.6449 41.4473 9.86859 43.2236 9.86859 45.3946C9.86859 47.5656 11.6449 49.3419 13.8159 49.3419C15.9869 49.3419 17.7632 47.5658 17.7632 45.3946C17.7632 43.2236 15.9869 41.4473 13.8159 41.4473Z" fill="white" fill-opacity="0.1"/>
                            <path d="M73.0262 31.579C74.1118 31.579 75 30.6908 75 29.6052C75 28.5196 74.1118 27.6315 73.0262 27.6315C71.9406 27.6315 71.0525 28.5196 71.0525 29.6052C71.0525 30.6908 71.9406 31.579 73.0262 31.579Z" fill="white" fill-opacity="0.1"/>
                            <path d="M45.3946 3.9473C46.4802 3.9473 47.3683 3.05914 47.3683 1.97356C47.3683 0.888156 46.4802 0 45.3946 0C44.309 0 43.4208 0.888156 43.4208 1.97374C43.421 3.05914 44.3092 3.9473 45.3946 3.9473Z" fill="white" fill-opacity="0.1"/>
                            <path d="M29.6053 17.7631C31.7762 17.7631 33.5526 15.9868 33.5526 13.8158C33.5526 11.6448 31.7762 9.86853 29.6053 9.86853C27.4343 9.86853 25.658 11.6448 25.658 13.8158C25.658 15.9868 27.4343 17.7631 29.6053 17.7631Z" fill="white" fill-opacity="0.1"/>
                            <path d="M29.6053 3.9473C30.6909 3.9473 31.579 3.05914 31.579 1.97356C31.579 0.888156 30.6909 0 29.6053 0C28.5197 0 27.6315 0.888156 27.6315 1.97374C27.6315 3.05914 28.5197 3.9473 29.6053 3.9473Z" fill="white" fill-opacity="0.1"/>
                            <path d="M45.3946 17.7631C47.5656 17.7631 49.3419 15.9868 49.3419 13.8158C49.3419 11.6448 47.5656 9.86853 45.3946 9.86853C43.2236 9.86853 41.4473 11.6448 41.4473 13.8158C41.4473 15.9868 43.2236 17.7631 45.3946 17.7631Z" fill="white" fill-opacity="0.1"/>
                            <path d="M13.8159 57.2367C11.6449 57.2367 9.86859 59.013 9.86859 61.184C9.86859 63.355 11.6449 65.1313 13.8159 65.1313C15.9869 65.1313 17.7632 63.3552 17.7632 61.1842C17.7632 59.013 15.9869 57.2367 13.8159 57.2367Z" fill="white" fill-opacity="0.1"/>
                            <path d="M45.3946 23.6842C42.1182 23.6842 39.4736 26.3288 39.4736 29.6052C39.4736 32.8816 42.1182 35.5263 45.3946 35.5263C48.671 35.5263 51.3157 32.8816 51.3157 29.6052C51.3157 26.3288 48.671 23.6842 45.3946 23.6842Z" fill="white" fill-opacity="0.1"/>
                            <path d="M61.1842 41.4473C59.0132 41.4473 57.2369 43.2236 57.2369 45.3946C57.2369 47.5656 59.0132 49.3419 61.1842 49.3419C63.3552 49.3419 65.1315 47.5656 65.1315 45.3946C65.1315 43.2236 63.3552 41.4473 61.1842 41.4473Z" fill="white" fill-opacity="0.1"/>
                            <path d="M61.1842 57.2367C59.0132 57.2367 57.2369 59.013 57.2369 61.184C57.2369 63.355 59.0132 65.1313 61.1842 65.1313C63.3552 65.1315 65.1315 63.3552 65.1315 61.1842C65.1315 59.013 63.3552 57.2367 61.1842 57.2367Z" fill="white" fill-opacity="0.1"/>
                            <path d="M61.1842 25.6579C59.0132 25.6579 57.2369 27.4342 57.2369 29.6052C57.2369 31.7762 59.0132 33.5525 61.1842 33.5525C63.3552 33.5525 65.1315 31.7762 65.1315 29.6052C65.1315 27.4342 63.3552 25.6579 61.1842 25.6579Z" fill="white" fill-opacity="0.1"/>
                            <path d="M73.0262 43.421C71.9406 43.421 71.0525 44.3092 71.0525 45.3948C71.0525 46.4802 71.9406 47.3683 73.0262 47.3683C74.1118 47.3683 75 46.4802 75 45.3946C74.9998 44.3092 74.1116 43.421 73.0262 43.421Z" fill="white" fill-opacity="0.1"/>
                            <path d="M61.1842 9.86835C59.0132 9.86835 57.2369 11.6447 57.2369 13.8156C57.2369 15.9866 59.013 17.7631 61.1842 17.7631C63.3552 17.7631 65.1315 15.9868 65.1315 13.8158C65.1315 11.6448 63.3552 9.86835 61.1842 9.86835Z" fill="white" fill-opacity="0.1"/>
                            <path d="M29.6052 39.4736C26.3289 39.4736 23.6842 42.1182 23.6842 45.3946C23.6842 48.671 26.3289 51.3157 29.6052 51.3157C32.8816 51.3157 35.5263 48.671 35.5263 45.3946C35.5263 42.1182 32.8816 39.4736 29.6052 39.4736Z" fill="white" fill-opacity="0.1"/>
                            <path d="M29.6053 57.2367C27.4343 57.2367 25.658 59.013 25.658 61.184C25.658 63.355 27.4343 65.1313 29.6053 65.1313C31.7762 65.1313 33.5526 63.355 33.5526 61.184C33.5526 59.013 31.7762 57.2367 29.6053 57.2367Z" fill="white" fill-opacity="0.1"/>
                            <path d="M29.6052 23.6842C26.3289 23.6842 23.6842 26.3288 23.6842 29.6052C23.6842 32.8816 26.3289 35.5263 29.6052 35.5263C32.8816 35.5263 35.5263 32.8816 35.5263 29.6052C35.5263 26.3288 32.8816 23.6842 29.6052 23.6842Z" fill="white" fill-opacity="0.1"/>
                            <path d="M45.3946 57.2367C43.2236 57.2367 41.4473 59.013 41.4473 61.184C41.4473 63.3552 43.2236 65.1315 45.3946 65.1315C47.5656 65.1315 49.3419 63.3552 49.3419 61.1842C49.3421 59.013 47.5658 57.2367 45.3946 57.2367Z" fill="white" fill-opacity="0.1"/>
                            <path d="M45.3946 71.0525C44.309 71.0525 43.4208 71.9406 43.4208 73.0262C43.4208 74.1118 44.309 75 45.3946 75C46.4802 75 47.3683 74.1118 47.3683 73.0262C47.3683 71.9406 46.4802 71.0525 45.3946 71.0525Z" fill="white" fill-opacity="0.1"/>
                            <path d="M45.3946 39.4736C42.1182 39.4736 39.4736 42.1182 39.4736 45.3946C39.4736 48.671 42.1182 51.3157 45.3946 51.3157C48.671 51.3157 51.3157 48.671 51.3157 45.3946C51.3157 42.1182 48.671 39.4736 45.3946 39.4736Z" fill="white" fill-opacity="0.1"/>
                        </svg>

                        <div class="suggestion-icon-wrap">
                            <i class="far fa-user"></i>

                        </div>
                    </div>
                    <ul class="phone-widget__list-item calls-history suggested-contacts">
                        <li class="calls-history__item contact-info-card ">
                            <div class="collapsible-toggler">
                                <div class="contact-info-card__status">
                                    <i class="far fa-user"></i>


                                </div>
                                <div class="contact-info-card__details">
                                    <div class="contact-info-card__line history-details">
                                        <strong class="contact-info-card__name">Geordan Reyney</strong>
                                    </div>



                                </div>
                            </div>
                        </li>
                        <li class="calls-history__item contact-info-card ">
                            <div class="collapsible-toggler">
                                <div class="contact-info-card__status">
                                    <i class="far fa-user"></i>


                                </div>
                                <div class="contact-info-card__details">
                                    <div class="contact-info-card__line history-details">
                                        <strong class="contact-info-card__name">Geordan Reyney</strong>
                                    </div>



                                </div>
                            </div>
                        </li>
                        <li class="calls-history__item contact-info-card ">
                            <div class="collapsible-toggler">
                                <div class="contact-info-card__status">
                                    <i class="far fa-user"></i>


                                </div>
                                <div class="contact-info-card__details">
                                    <div class="contact-info-card__line history-details">
                                        <strong class="contact-info-card__name">Geordan Reyney</strong>
                                    </div>



                                </div>
                            </div>
                        </li>
                        <li class="calls-history__item contact-info-card">
                            <div class="collapsible-toggler">
                                <div class="contact-info-card__status">
                                    <i class="far fa-user"></i>


                                </div>
                                <div class="contact-info-card__details">
                                    <div class="contact-info-card__line history-details">
                                        <strong class="contact-info-card__name">Geordan Reyney</strong>
                                    </div>



                                </div>
                            </div>
                        </li>

                    </ul>
                    <input type="text" class="call-pane__dial-number" id="call-pane__dial-number" value="" placeholder="Add Number">
                    <a href="#" class="call-pane__dial-clear-all">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
                                    fill="white" />
                        </svg>
                    </a>

                </div>
                <div class="call-pane__dial-block">
                    <ul class="call-pane__dial dial">
                        <li class="dial__item"><button class="dial__btn" value="1">1</button></li>
                        <li class="dial__item"><button class="dial__btn" value="2">2</button></li>
                        <li class="dial__item"><button class="dial__btn" value="3">3</button></li>
                        <li class="dial__item"><button class="dial__btn" value="4">4</button></li>
                        <li class="dial__item"><button class="dial__btn" value="5">5</button></li>
                        <li class="dial__item"><button class="dial__btn" value="6">6</button></li>
                        <li class="dial__item"><button class="dial__btn" value="7">7</button></li>
                        <li class="dial__item"><button class="dial__btn" value="8">8</button></li>
                        <li class="dial__item"><button class="dial__btn" value="9">9</button></li>
                        <li class="dial__item"><button class="dial__btn" value="✱">✱</button></li>
                        <li class="dial__item"><button class="dial__btn" value="0">0</button></li>
                        <li class="dial__item"><button class="dial__btn" value="#">#</button></li>
                    </ul>
                    <div class="call-pane__call-btns">
                        <button class="call-pane__start-call calling-state-block" id="btn-make-call">
                            <div class="call-in-action">
                                <span class="call-in-action__text">Calling</span>
                                <span class="call-in-action__time">01:54</span>
                            </div>
                            <i class="fas fa-phone"></i>
                        </button>
                        <button class="call-pane__end-call">
                            <i class="fas fa-phone-slash"></i>
                        </button>
                        <button class="call-pane__correction">
                            <i class="fas fa-backspace"></i>
                        </button>
                    </div>
                </div>
                <div class="call-pane__note-block">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M4.5778 11.0407C4.5778 11.2514 4.74859 11.4222 4.95928 11.4222H7.49903C7.70138 11.4222 7.89544 11.3418 8.03853 11.1987L15.7766 3.46065C15.9196 3.31757 16 3.12355 16 2.92123C16 2.71892 15.9196 2.52489 15.7766 2.38182L13.6182 0.223386C13.4751 0.0803521 13.2811 0 13.0788 0C12.8765 0 12.6824 0.0803521 12.5393 0.223386L4.80126 7.96147C4.65818 8.10455 4.5778 8.29862 4.5778 8.50097V11.0407ZM14.1576 2.92123L7.18256 9.89627H6.10373V8.81744L13.0788 1.8424L14.1576 2.92123Z"
                              fill="#446D97"></path>
                        <path
                                d="M1.52593 14.474V2.26655H5.34076V0.740614H1.52593C0.683183 0.740614 0 1.4238 0 2.26655V14.474C0 15.3168 0.683184 15.9999 1.52593 15.9999H13.7334C14.5761 15.9999 15.2593 15.3168 15.2593 14.474V10.6592H13.7334V14.474H1.52593Z"
                                fill="#446D97"></path>
                    </svg>

                    <div class="form-group">
                        <input type="text" class="call-pane__note-msg form-control" placeholder="Add Note">
                        <div class="error-message"></div>
                    </div>
                    <button class="call-pane__add-note">
                        <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M16.7072 1.70718L6.50008 11.9143L0.292969 5.70718L1.70718 4.29297L6.50008 9.08586L15.293 0.292969L16.7072 1.70718Z"
                                  fill="white" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="phone-widget__tab " id="tab-contacts">
            <div class="contacts__search-wrap">
                <label class="contacts__icon" for="">
                    <i class="fa fa-search"></i>
                </label>
                <input type="text" class="contacts__search-input" placeholder="Search Contacts">
            </div>
            <span class="section-separator">G</span>

            <ul class="phone-widget__list-item calls-history">
                <li class="calls-history__item contact-info-card is-collapsible">
                    <div class="collapsible-toggler collapsed" data-toggle="collapse" data-target="#collapseOne"
                         aria-expanded="true" aria-controls="collapseOne">
                        <div class="contact-info-card__status">
                            <div class="agent-text-avatar">
          <span>
            A
          </span>
                            </div>

                        </div>
                        <div class="contact-info-card__details">
                            <div class="contact-info-card__line history-details">
                                <strong class="contact-info-card__name">Geordan Reyney</strong>
                            </div>

                            <div class="contact-info-card__line history-details">
                                <span class="contact-info-card__call-type">Chief Marketing Officer</span>
                            </div>
                            <a href="#" class="collapsible-arrow"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <ul class="contact-options-list">
                                <li class="contact-options-list__option js-toggle-contact-info">
                                    <i class="fa fa-user"></i>
                                    <span>View</span>
                                </li>
                                <li class="contact-options-list__option">
                                    <i class="fa fa-comment-alt"></i>
                                    <span>SMS</span>
                                </li>

                                <li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">
                                    <i class="fa fa-phone"></i>
                                    <span>Call</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </li>
                <li class="calls-history__item contact-info-card is-collapsible">
                    <div class="collapsible-toggler collapsed" data-toggle="collapse" data-target="#collapseTwo"
                         aria-expanded="true" aria-controls="collapseTwo">
                        <div class="contact-info-card__status">
                            <div class="agent-text-avatar">
          <span>
            A
          </span>
                            </div>

                        </div>
                        <div class="contact-info-card__details">
                            <div class="contact-info-card__line history-details">
                                <strong class="contact-info-card__name">Geordan Reyney</strong>
                            </div>

                            <div class="contact-info-card__line history-details">
                                <span class="contact-info-card__call-type">Chief Marketing Officer</span>
                            </div>
                            <a href="#" class="collapsible-arrow " ><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div id="collapseTwo" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <ul class="contact-options-list">
                                <li class="contact-options-list__option js-toggle-contact-info">
                                    <i class="fa fa-user"></i>
                                    <span>View</span>
                                </li>
                                <li class="contact-options-list__option">
                                    <i class="fa fa-comment-alt"></i>
                                    <span>SMS</span>
                                </li>

                                <li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">
                                    <i class="fa fa-phone"></i>
                                    <span>Call</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </li>
                <li class="calls-history__item contact-info-card is-collapsible">
                    <div class="collapsible-toggler collapsed" data-toggle="collapse" data-target="#collapseThree"
                         aria-expanded="true" aria-controls="collapseThree">
                        <div class="contact-info-card__status">
                            <div class="agent-text-avatar">
          <span>
            A
          </span>
                            </div>

                        </div>
                        <div class="contact-info-card__details">
                            <div class="contact-info-card__line history-details">
                                <strong class="contact-info-card__name">Geordan Reyney</strong>
                            </div>

                            <div class="contact-info-card__line history-details">
                                <span class="contact-info-card__call-type">Chief Marketing Officer</span>
                            </div>
                            <a href="#" class="collapsible-arrow " ><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div id="collapseThree" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <ul class="contact-options-list">
                                <li class="contact-options-list__option js-toggle-contact-info">
                                    <i class="fa fa-user"></i>
                                    <span>View</span>
                                </li>
                                <li class="contact-options-list__option">
                                    <i class="fa fa-comment-alt"></i>
                                    <span>SMS</span>
                                </li>

                                <li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">
                                    <i class="fa fa-phone"></i>
                                    <span>Call</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </li>
            </ul>

            <div class="widget-phone__contact-info-modal contact-modal-info">
                <a href="#" class="contact-modal-info__close">
                    <i class="fa fa-arrow-left"></i>
                    Back to contacts</i></a>
                <div class="contact-modal-info__user">
                    <div class="agent-text-avatar">
      <span>
        A
      </span>
                    </div>
                    <h3 class="contact-modal-info__name">Geordan Reyney</h3>

                    <div class="contact-modal-info__actions">
                        <ul class="contact-options-list">
                            <li class="contact-options-list__option js-edit-mode">
                                <i class="fa fa-user"></i>
                                <span>EDIT</span>
                            </li>
                            <li class="contact-options-list__option">
                                <i class="fa fa-comment-alt"></i>
                                <span>SMS</span>
                            </li>

                            <li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">
                                <i class="fa fa-phone"></i>
                                <span>Call</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="contact-modal-info__body">
                    <ul class="contact-modal-info__contacts contact-full-info">
                        <li>
                            <div class="form-group">
                                <label for="">Type</label>
                                <div class="form-control-wrap" data-type="company">

                                    <i class="fa fa-building contact-type-company"></i>
                                    <i class="fa fa-user contact-type-person"></i>
                                    <select readonly type="text" class="form-control select-contact-type" value="Company" autocomplete="off" readonly disabled>
                                        <option value="company">Company</option>
                                        <option value="person">Person</option>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-group">
                                <label for="">Phone 1</label>
                                <input readonly type="text" class="form-control" value="+373-69-223344" autocomplete="off">
                            </div>
                            <ul class="actions-list">
                                <li class="actions-list__option actions-list__option--phone js-call-tab-trigger">
                                    <i class="fa fa-phone"></i>
                                </li>
                                <li class="actions-list__option">
                                    <i class="fa fa-comment-alt"></i>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="form-group">
                                <label for="">Phone 2</label>
                                <input readonly type="text" class="form-control" value="+1-222-555-8888" autocomplete="off">
                            </div>

                            <ul class="actions-list">
                                <li class="actions-list__option actions-list__option--phone js-call-tab-trigger">
                                    <i class="fa fa-phone"></i>
                                </li>
                                <li class="actions-list__option">
                                    <i class="fa fa-comment-alt"></i>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="form-group">
                                <label for="">Email</label>
                                <input readonly type="email" class="form-control" value="andrew.johnson@gttglobal.com" autocomplete="off">
                            </div>
                            <ul class="actions-list">
                                <li class="actions-list__option">
                                    <i class="fa fa-envelope"></i>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="form-group">
                                <label for="">Post</label>
                                <input readonly type="text" class="form-control" value="Head of Ticketing Department" autocomplete="off">
                            </div>
                        </li>
                        <li>
                            <div class="form-group">
                                <label for="">Date of birth</label>
                                <input readonly type="text" class="form-control" value="24-03-1972" autocomplete="off">
                            </div>
                        </li>


                    </ul>

                    <a href="#" class="contact-modal-info__remove-contact">DELETE CONTACT</a>
                </div>

            </div>
        </div>
        <div class="phone-widget__tab" id="tab-history">
            <span class="section-separator">Today</span>
            <ul class="phone-widget__list-item calls-history">
                <li class="calls-history__item contact-info-card">
                    <div class="contact-info-card__status">
                        <div class="contact-info-card__call-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0)">
                                    <path
                                            d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                            fill="#33404F" />
                                    <path d="M12.5 1.66666V7.49999M12.5 7.49999H18.3333M12.5 7.49999L18.3333 1.66666" stroke="#008344"
                                          stroke-width="2" />
                                </g>
                                <defs>
                                    <clipPath id="clip0">
                                        <path d="M0 0L20 0V20H0L0 0Z" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>


                    </div>
                    <div class="contact-info-card__details">
                        <div class="contact-info-card__line history-details">
                            <strong class="contact-info-card__name">Geordan Reyney</strong>
                            <small class="contact-info-card__timestamp">10.30 PM</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <span class="contact-info-card__call-type">Inbound Call</span>
                            <small class="contact-info-card__call-length">20m</small>
                        </div>
                    </div>
                </li>
                <li class="calls-history__item contact-info-card">
                    <div class="contact-info-card__status">
                        <div class="contact-info-card__call-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                        fill="#33404F" />
                                <path d="M18.3333 1.66667L12.5 7.5M12.5 1.66667L18.3333 7.5" stroke="#C60000" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                    <div class="contact-info-card__details">
                        <div class="contact-info-card__line history-details">
                            <strong class="contact-info-card__name">Geordan Reyney</strong>
                            <small class="contact-info-card__timestamp">10.30 PM</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <span class="contact-info-card__call-type">Missed Call</span>
                            <small class="contact-info-card__call-length">15m</small>
                        </div>
                    </div>
                </li>
                <li class="calls-history__item contact-info-card">
                    <div class="contact-info-card__status">
                        <div class="contact-info-card__call-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                        fill="#33404F" />
                                <path d="M18.3333 1.66667L12.5 7.5M12.5 1.66667L18.3333 7.5" stroke="#C60000" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                    <div class="contact-info-card__details">
                        <div class="contact-info-card__line history-details">
                            <strong class="contact-info-card__name">Geordan Reyney</strong>
                            <small class="contact-info-card__timestamp">10.30 PM</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <span class="contact-info-card__call-type">Missed Call</span>
                            <small class="contact-info-card__call-length">15m</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <div class="contact-info-card__note">
                                <span class="contact-info-card__note-message">Call with Marketers</span>
                            </div>

                        </div>
                    </div>
                </li>
            </ul>
            <span class="section-separator">Yesterday</span>
            <ul class="phone-widget__list-item calls-history">
                <li class="calls-history__item contact-info-card">
                    <div class="contact-info-card__status">
                        <div class="contact-info-card__call-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0)">
                                    <path
                                            d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                            fill="#33404F" />
                                    <path d="M12.5 1.66666V7.49999M12.5 7.49999H18.3333M12.5 7.49999L18.3333 1.66666" stroke="#008344"
                                          stroke-width="2" />
                                </g>
                                <defs>
                                    <clipPath id="clip0">
                                        <path d="M0 0L20 0V20H0L0 0Z" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>


                    </div>
                    <div class="contact-info-card__details">
                        <div class="contact-info-card__line history-details">
                            <strong class="contact-info-card__name">Geordan Reyney</strong>
                            <small class="contact-info-card__timestamp">10.30 PM</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <span class="contact-info-card__call-type">Inbound Call</span>
                            <small class="contact-info-card__call-length">20m</small>
                        </div>
                    </div>
                </li>
                <li class="calls-history__item contact-info-card">
                    <div class="contact-info-card__status">
                        <div class="contact-info-card__call-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                        fill="#33404F" />
                                <path d="M18.3333 1.66667L12.5 7.5M12.5 1.66667L18.3333 7.5" stroke="#C60000" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                    <div class="contact-info-card__details">
                        <div class="contact-info-card__line history-details">
                            <strong class="contact-info-card__name">Geordan Reyney</strong>
                            <small class="contact-info-card__timestamp">10.30 PM</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <span class="contact-info-card__call-type">Missed Call</span>
                            <small class="contact-info-card__call-length">15m</small>
                        </div>
                    </div>
                </li>
                <li class="calls-history__item contact-info-card">
                    <div class="contact-info-card__status">
                        <div class="contact-info-card__call-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                        fill="#33404F" />
                                <path d="M18.3333 1.66667L12.5 7.5M12.5 1.66667L18.3333 7.5" stroke="#C60000" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                    <div class="contact-info-card__details">
                        <div class="contact-info-card__line history-details">
                            <strong class="contact-info-card__name">Geordan Reyney</strong>
                            <small class="contact-info-card__timestamp">10.30 PM</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <span class="contact-info-card__call-type">Missed Call</span>
                            <small class="contact-info-card__call-length">15m</small>
                        </div>
                        <div class="contact-info-card__line history-details">
                            <div class="contact-info-card__note">
                                <span class="contact-info-card__note-message">Call with Marketers</span>
                            </div>

                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
	<?php else: ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong> WebCall token is empty.
        </div>
	<?php endif; ?>
</div>