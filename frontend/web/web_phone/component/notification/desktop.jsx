function DesktopNotification(props) {
    return (
        <div className="phone-notifications">
            <ul className="phone-notifications__list">
                <li className="phone-notifications__item phone-notifications__item--shown">
                    <div className="incoming-notification">
                        <i className="user-icn">G</i>
                        <div className="incoming-notification__inner">
                            <div className="incoming-notification__info">
                                <div className="incoming-notification__general-info">
                                    <b className="incoming-notification__name">Geffy Morgan Jefferson</b>
                                    <span className="incoming-notification__phone">+1 (888) 88 888 88</span>
                                    <div className="incoming-notification__project">
                                        <b className="incoming-notification__project-name">WOWFARE</b>
                                        <i>•</i>
                                        <span className="incoming-notification__position">Sales General</span>
                                    </div>
                                </div>
                                <div className="incoming-notification__timer">
                                    <span>24:32</span>
                                </div>

                            </div>
                            <div className="incoming-notification__action-list">
                                <div className="incoming-notification__dynamic">
                                    <a href="#"
                                       className="incoming-notification__action incoming-notification__action--line">
                                        <i className="fa fa-random"></i>
                                    </a>

                                    <a href="#"
                                       className="incoming-notification__action incoming-notification__action--info">
                                        <i className="fa fa-info"></i>
                                    </a>

                                    <a href="#"
                                       className="incoming-notification__action incoming-notification__action--phone">
                                        <i className="fa fa-phone"></i>
                                    </a>
                                </div>
                                <a href="#"
                                   className="incoming-notification__action incoming-notification__action--max">
                                    <i className="fa fa-long-arrow-down"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    );
}