--
-- PostgreSQL database dump
--

-- Dumped from database version 12.8 (Ubuntu 12.8-1.pgdg20.04+1)
-- Dumped by pg_dump version 13.4 (Ubuntu 13.4-1.pgdg20.04+1)

-- Started on 2021-09-20 13:30:59 EEST

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

--
-- TOC entry 202 (class 1259 OID 16386)
-- Name: api_log; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.api_log (
    al_id integer NOT NULL,
    al_request_data text,
    al_request_dt timestamp(0) without time zone,
    al_response_data text,
    al_response_dt timestamp(0) without time zone,
    al_ip_address character varying(40),
    al_user_id integer,
    al_action character varying(255),
    al_execution_time numeric(6,3),
    al_memory_usage integer,
    al_db_execution_time numeric(6,3),
    al_db_query_count integer,
    al_created_dt timestamp(0) without time zone NOT NULL
)
PARTITION BY RANGE (al_created_dt);


--
-- TOC entry 208 (class 1259 OID 16419)
-- Name: api_log_al_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.api_log ALTER COLUMN al_id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.api_log_al_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


SET default_table_access_method = heap;

--
-- TOC entry 209 (class 1259 OID 16421)
-- Name: client_chat_canned_response; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.client_chat_canned_response (
    cr_id integer NOT NULL,
    cr_project_id integer,
    cr_category_id integer,
    cr_language_id character varying(5) DEFAULT NULL::character varying,
    cr_user_id integer,
    cr_sort_order smallint,
    cr_message text,
    cr_created_dt timestamp(0) without time zone,
    cr_updated_dt timestamp(0) without time zone
);


--
-- TOC entry 210 (class 1259 OID 16428)
-- Name: client_chat_canned_response_category; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.client_chat_canned_response_category (
    crc_id integer NOT NULL,
    crc_name character varying(50) NOT NULL,
    crc_enabled boolean,
    crc_created_dt timestamp(0) without time zone,
    crc_updated_dt timestamp(0) without time zone,
    crc_created_user_id integer,
    crc_updated_user_id integer
);


--
-- TOC entry 211 (class 1259 OID 16431)
-- Name: client_chat_canned_response_category_crc_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.client_chat_canned_response_category_crc_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3239 (class 0 OID 0)
-- Dependencies: 211
-- Name: client_chat_canned_response_category_crc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.client_chat_canned_response_category_crc_id_seq OWNED BY public.client_chat_canned_response_category.crc_id;


--
-- TOC entry 212 (class 1259 OID 16433)
-- Name: client_chat_canned_response_cr_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.client_chat_canned_response_cr_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3240 (class 0 OID 0)
-- Dependencies: 212
-- Name: client_chat_canned_response_cr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.client_chat_canned_response_cr_id_seq OWNED BY public.client_chat_canned_response.cr_id;


--
-- TOC entry 213 (class 1259 OID 16435)
-- Name: client_chat_message; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.client_chat_message (
    ccm_id bigint NOT NULL,
    ccm_rid character varying(150) NOT NULL,
    ccm_client_id integer,
    ccm_user_id integer,
    ccm_sent_dt timestamp(0) without time zone NOT NULL,
    ccm_has_attachment smallint DEFAULT 0,
    ccm_body jsonb NOT NULL,
    ccm_cch_id integer,
    ccm_event smallint,
    ccm_platform_id smallint DEFAULT 1
)
PARTITION BY RANGE (ccm_sent_dt);


--
-- TOC entry 228 (class 1259 OID 16552)
-- Name: client_chat_message_ccm_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.client_chat_message ALTER COLUMN ccm_id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.client_chat_message_ccm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 229 (class 1259 OID 16554)
-- Name: client_chat_request; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.client_chat_request (
    ccr_id bigint NOT NULL,
    ccr_event smallint,
    ccr_rid character varying(150),
    ccr_json_data text,
    ccr_created_dt timestamp(0) without time zone NOT NULL,
    ccr_visitor_id character varying(100),
    ccr_job_id integer
)
PARTITION BY RANGE (ccr_created_dt);


--
-- TOC entry 232 (class 1259 OID 16569)
-- Name: client_chat_request_ccr_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.client_chat_request ALTER COLUMN ccr_id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.client_chat_request_ccr_id_seq
    START WITH 1
    INCREMENT BY 2
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 233 (class 1259 OID 16571)
-- Name: file_case; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_case (
    fc_fs_id integer NOT NULL,
    fc_case_id integer NOT NULL
);


--
-- TOC entry 234 (class 1259 OID 16574)
-- Name: file_client; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_client (
    fcl_fs_id integer NOT NULL,
    fcl_client_id integer NOT NULL
);


--
-- TOC entry 235 (class 1259 OID 16577)
-- Name: file_lead; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_lead (
    fld_fs_id integer NOT NULL,
    fld_lead_id integer NOT NULL
);


--
-- TOC entry 236 (class 1259 OID 16580)
-- Name: file_log; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_log (
    fl_id integer NOT NULL,
    fl_fs_id integer,
    fl_fsh_id integer,
    fl_type_id smallint,
    fl_created_dt timestamp(0) without time zone,
    fl_ip_address character varying(40),
    fl_user_agent character varying(500)
);


--
-- TOC entry 237 (class 1259 OID 16586)
-- Name: file_log_fl_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.file_log_fl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3241 (class 0 OID 0)
-- Dependencies: 237
-- Name: file_log_fl_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.file_log_fl_id_seq OWNED BY public.file_log.fl_id;


--
-- TOC entry 238 (class 1259 OID 16588)
-- Name: file_order; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_order (
    fo_id integer NOT NULL,
    fo_fs_id integer,
    fo_or_id integer,
    fo_pq_id integer,
    fo_category_id integer,
    fo_created_dt timestamp(0) without time zone
);


--
-- TOC entry 239 (class 1259 OID 16591)
-- Name: file_order_fo_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.file_order_fo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3242 (class 0 OID 0)
-- Dependencies: 239
-- Name: file_order_fo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.file_order_fo_id_seq OWNED BY public.file_order.fo_id;


--
-- TOC entry 240 (class 1259 OID 16593)
-- Name: file_product_quote; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_product_quote (
    fpq_fs_id integer NOT NULL,
    fpq_pq_id integer NOT NULL,
    fpq_created_dt timestamp(0) without time zone
);


--
-- TOC entry 241 (class 1259 OID 16596)
-- Name: file_share; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_share (
    fsh_id integer NOT NULL,
    fsh_fs_id integer,
    fsh_code character varying(32),
    fsh_expired_dt timestamp(0) without time zone,
    fsh_created_dt timestamp(0) without time zone,
    fsh_created_user_id integer
);


--
-- TOC entry 242 (class 1259 OID 16599)
-- Name: file_share_fsh_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.file_share_fsh_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3243 (class 0 OID 0)
-- Dependencies: 242
-- Name: file_share_fsh_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.file_share_fsh_id_seq OWNED BY public.file_share.fsh_id;


--
-- TOC entry 243 (class 1259 OID 16601)
-- Name: file_storage; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_storage (
    fs_id integer NOT NULL,
    fs_uid character varying(32),
    fs_mime_type character varying(127),
    fs_name character varying(100),
    fs_title character varying(100),
    fs_path character varying(250),
    fs_size integer,
    fs_private boolean,
    fs_md5_hash character varying(32),
    fs_status smallint,
    fs_expired_dt timestamp(0) without time zone,
    fs_created_dt timestamp(0) without time zone
);


--
-- TOC entry 244 (class 1259 OID 16607)
-- Name: file_storage_fs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.file_storage_fs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3244 (class 0 OID 0)
-- Dependencies: 244
-- Name: file_storage_fs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.file_storage_fs_id_seq OWNED BY public.file_storage.fs_id;


--
-- TOC entry 245 (class 1259 OID 16609)
-- Name: file_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.file_user (
    fus_fs_id integer NOT NULL,
    fus_user_id integer NOT NULL
);


--
-- TOC entry 246 (class 1259 OID 16612)
-- Name: log; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.log (
    id integer NOT NULL,
    level integer,
    category character varying(255),
    log_time double precision,
    prefix text,
    message text
);


--
-- TOC entry 247 (class 1259 OID 16618)
-- Name: log_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.log_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3245 (class 0 OID 0)
-- Dependencies: 247
-- Name: log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.log_id_seq OWNED BY public.log.id;


--
-- TOC entry 3018 (class 2604 OID 16620)
-- Name: client_chat_canned_response cr_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_canned_response ALTER COLUMN cr_id SET DEFAULT nextval('public.client_chat_canned_response_cr_id_seq'::regclass);


--
-- TOC entry 3019 (class 2604 OID 16621)
-- Name: client_chat_canned_response_category crc_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_canned_response_category ALTER COLUMN crc_id SET DEFAULT nextval('public.client_chat_canned_response_category_crc_id_seq'::regclass);


--
-- TOC entry 3022 (class 2604 OID 16622)
-- Name: file_log fl_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_log ALTER COLUMN fl_id SET DEFAULT nextval('public.file_log_fl_id_seq'::regclass);


--
-- TOC entry 3023 (class 2604 OID 16623)
-- Name: file_order fo_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_order ALTER COLUMN fo_id SET DEFAULT nextval('public.file_order_fo_id_seq'::regclass);


--
-- TOC entry 3024 (class 2604 OID 16624)
-- Name: file_share fsh_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_share ALTER COLUMN fsh_id SET DEFAULT nextval('public.file_share_fsh_id_seq'::regclass);


--
-- TOC entry 3025 (class 2604 OID 16625)
-- Name: file_storage fs_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_storage ALTER COLUMN fs_id SET DEFAULT nextval('public.file_storage_fs_id_seq'::regclass);


--
-- TOC entry 3026 (class 2604 OID 16626)
-- Name: log id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.log ALTER COLUMN id SET DEFAULT nextval('public.log_id_seq'::regclass);


--
-- TOC entry 3213 (class 0 OID 16421)
-- Dependencies: 209
-- Data for Name: client_chat_canned_response; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.client_chat_canned_response (cr_id, cr_project_id, cr_category_id, cr_language_id, cr_user_id, cr_sort_order, cr_message, cr_created_dt, cr_updated_dt) FROM stdin;
\.


--
-- TOC entry 3214 (class 0 OID 16428)
-- Dependencies: 210
-- Data for Name: client_chat_canned_response_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.client_chat_canned_response_category (crc_id, crc_name, crc_enabled, crc_created_dt, crc_updated_dt, crc_created_user_id, crc_updated_user_id) FROM stdin;
\.


--
-- TOC entry 3219 (class 0 OID 16571)
-- Dependencies: 233
-- Data for Name: file_case; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_case (fc_fs_id, fc_case_id) FROM stdin;
\.


--
-- TOC entry 3220 (class 0 OID 16574)
-- Dependencies: 234
-- Data for Name: file_client; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_client (fcl_fs_id, fcl_client_id) FROM stdin;
\.


--
-- TOC entry 3221 (class 0 OID 16577)
-- Dependencies: 235
-- Data for Name: file_lead; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_lead (fld_fs_id, fld_lead_id) FROM stdin;
\.


--
-- TOC entry 3222 (class 0 OID 16580)
-- Dependencies: 236
-- Data for Name: file_log; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_log (fl_id, fl_fs_id, fl_fsh_id, fl_type_id, fl_created_dt, fl_ip_address, fl_user_agent) FROM stdin;
\.


--
-- TOC entry 3224 (class 0 OID 16588)
-- Dependencies: 238
-- Data for Name: file_order; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_order (fo_id, fo_fs_id, fo_or_id, fo_pq_id, fo_category_id, fo_created_dt) FROM stdin;
\.


--
-- TOC entry 3226 (class 0 OID 16593)
-- Dependencies: 240
-- Data for Name: file_product_quote; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_product_quote (fpq_fs_id, fpq_pq_id, fpq_created_dt) FROM stdin;
\.


--
-- TOC entry 3227 (class 0 OID 16596)
-- Dependencies: 241
-- Data for Name: file_share; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_share (fsh_id, fsh_fs_id, fsh_code, fsh_expired_dt, fsh_created_dt, fsh_created_user_id) FROM stdin;
\.


--
-- TOC entry 3229 (class 0 OID 16601)
-- Dependencies: 243
-- Data for Name: file_storage; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_storage (fs_id, fs_uid, fs_mime_type, fs_name, fs_title, fs_path, fs_size, fs_private, fs_md5_hash, fs_status, fs_expired_dt, fs_created_dt) FROM stdin;
\.


--
-- TOC entry 3231 (class 0 OID 16609)
-- Dependencies: 245
-- Data for Name: file_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.file_user (fus_fs_id, fus_user_id) FROM stdin;
\.


--
-- TOC entry 3232 (class 0 OID 16612)
-- Dependencies: 246
-- Data for Name: log; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.log (id, level, category, log_time, prefix, message) FROM stdin;
1	4	info\\ws:actionStart	1632116225.632	[alex-connor][console]	console\\controllers\\WebsocketServerController::actionStart
2	4	info\\ws:actionStart:event:workerStart	1632116225.7838	[alex-connor][console]	Websocket Worker (Id: 0)  start: 2021-09-20 05:37:05
34	4	info\\ws:actionStart	1632116384.7909	[alex-connor][console]	console\\controllers\\WebsocketServerController::actionStart
35	4	info\\ws:actionStart:event:workerStart	1632116384.9162	[alex-connor][console]	Websocket Worker (Id: 0)  start: 2021-09-20 05:39:44
\.


--
-- TOC entry 3246 (class 0 OID 0)
-- Dependencies: 208
-- Name: api_log_al_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.api_log_al_id_seq', 1, false);


--
-- TOC entry 3247 (class 0 OID 0)
-- Dependencies: 211
-- Name: client_chat_canned_response_category_crc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.client_chat_canned_response_category_crc_id_seq', 1, false);


--
-- TOC entry 3248 (class 0 OID 0)
-- Dependencies: 212
-- Name: client_chat_canned_response_cr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.client_chat_canned_response_cr_id_seq', 1, false);


--
-- TOC entry 3249 (class 0 OID 0)
-- Dependencies: 228
-- Name: client_chat_message_ccm_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.client_chat_message_ccm_id_seq', 1, false);


--
-- TOC entry 3250 (class 0 OID 0)
-- Dependencies: 232
-- Name: client_chat_request_ccr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.client_chat_request_ccr_id_seq', 1, false);


--
-- TOC entry 3251 (class 0 OID 0)
-- Dependencies: 237
-- Name: file_log_fl_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.file_log_fl_id_seq', 1, false);


--
-- TOC entry 3252 (class 0 OID 0)
-- Dependencies: 239
-- Name: file_order_fo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.file_order_fo_id_seq', 1, false);


--
-- TOC entry 3253 (class 0 OID 0)
-- Dependencies: 242
-- Name: file_share_fsh_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.file_share_fsh_id_seq', 1, false);


--
-- TOC entry 3254 (class 0 OID 0)
-- Dependencies: 244
-- Name: file_storage_fs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.file_storage_fs_id_seq', 1, false);


--
-- TOC entry 3255 (class 0 OID 0)
-- Dependencies: 247
-- Name: log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.log_id_seq', 35, true);


--
-- TOC entry 3030 (class 2606 OID 16628)
-- Name: api_log PK-al_id-al_created_dt; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.api_log
    ADD CONSTRAINT "PK-al_id-al_created_dt" PRIMARY KEY (al_id, al_created_dt);


--
-- TOC entry 3038 (class 2606 OID 16630)
-- Name: client_chat_message PK-client_chat_message_ccm_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_message
    ADD CONSTRAINT "PK-client_chat_message_ccm_id" PRIMARY KEY (ccm_id, ccm_sent_dt);


--
-- TOC entry 3043 (class 2606 OID 16632)
-- Name: client_chat_request PK-client_chat_request; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_request
    ADD CONSTRAINT "PK-client_chat_request" PRIMARY KEY (ccr_id, ccr_created_dt);


--
-- TOC entry 3045 (class 2606 OID 16634)
-- Name: file_case PK-file_case-fs_id-case_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_case
    ADD CONSTRAINT "PK-file_case-fs_id-case_id" PRIMARY KEY (fc_fs_id, fc_case_id);


--
-- TOC entry 3047 (class 2606 OID 16636)
-- Name: file_client PK-file_client-fs_id-client_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_client
    ADD CONSTRAINT "PK-file_client-fs_id-client_id" PRIMARY KEY (fcl_fs_id, fcl_client_id);


--
-- TOC entry 3049 (class 2606 OID 16638)
-- Name: file_lead PK-file_lead-fs_id-lead_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_lead
    ADD CONSTRAINT "PK-file_lead-fs_id-lead_id" PRIMARY KEY (fld_fs_id, fld_lead_id);


--
-- TOC entry 3059 (class 2606 OID 16640)
-- Name: file_product_quote PK-file_product_quote-fpq_fs_id-fpq_pq_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_product_quote
    ADD CONSTRAINT "PK-file_product_quote-fpq_fs_id-fpq_pq_id" PRIMARY KEY (fpq_fs_id, fpq_pq_id);


--
-- TOC entry 3070 (class 2606 OID 16642)
-- Name: file_user PK-file_user-fs_id-user_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_user
    ADD CONSTRAINT "PK-file_user-fs_id-user_id" PRIMARY KEY (fus_fs_id, fus_user_id);


--
-- TOC entry 3034 (class 2606 OID 16654)
-- Name: client_chat_canned_response_category client_chat_canned_response_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_canned_response_category
    ADD CONSTRAINT client_chat_canned_response_category_pkey PRIMARY KEY (crc_id);


--
-- TOC entry 3032 (class 2606 OID 16656)
-- Name: client_chat_canned_response client_chat_canned_response_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_canned_response
    ADD CONSTRAINT client_chat_canned_response_pkey PRIMARY KEY (cr_id);


--
-- TOC entry 3051 (class 2606 OID 16690)
-- Name: file_log file_log_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_log
    ADD CONSTRAINT file_log_pkey PRIMARY KEY (fl_id);


--
-- TOC entry 3056 (class 2606 OID 16692)
-- Name: file_order file_order_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_order
    ADD CONSTRAINT file_order_pkey PRIMARY KEY (fo_id);


--
-- TOC entry 3063 (class 2606 OID 16694)
-- Name: file_share file_share_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_share
    ADD CONSTRAINT file_share_pkey PRIMARY KEY (fsh_id);


--
-- TOC entry 3068 (class 2606 OID 16696)
-- Name: file_storage file_storage_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_storage
    ADD CONSTRAINT file_storage_pkey PRIMARY KEY (fs_id);


--
-- TOC entry 3075 (class 2606 OID 16698)
-- Name: log log_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.log
    ADD CONSTRAINT log_pkey PRIMARY KEY (id);


--
-- TOC entry 3027 (class 1259 OID 16699)
-- Name: IDX_api_log_al_action; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IDX_api_log_al_action" ON ONLY public.api_log USING btree (al_action);


--
-- TOC entry 3028 (class 1259 OID 16700)
-- Name: IDX_api_log_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IDX_api_log_index" ON ONLY public.api_log USING btree (al_user_id, al_request_dt);


--
-- TOC entry 3071 (class 1259 OID 16701)
-- Name: IDX_category; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IDX_category" ON public.log USING btree (category);


--
-- TOC entry 3072 (class 1259 OID 16702)
-- Name: IDX_level; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IDX_level" ON public.log USING btree (level);


--
-- TOC entry 3073 (class 1259 OID 16703)
-- Name: IDX_log_time; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IDX_log_time" ON public.log USING btree (log_time);


--
-- TOC entry 3040 (class 1259 OID 16704)
-- Name: IND-ccr_event; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-ccr_event" ON ONLY public.client_chat_request USING btree (ccr_event);


--
-- TOC entry 3041 (class 1259 OID 16705)
-- Name: IND-ccr_rid; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-ccr_rid" ON ONLY public.client_chat_request USING btree (ccr_rid);


--
-- TOC entry 3035 (class 1259 OID 16706)
-- Name: IND-client_chat_message-ccm_cch_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-client_chat_message-ccm_cch_id" ON ONLY public.client_chat_message USING btree (ccm_cch_id);


--
-- TOC entry 3036 (class 1259 OID 16707)
-- Name: IND-client_chat_message-ccm_rid; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-client_chat_message-ccm_rid" ON ONLY public.client_chat_message USING btree (ccm_rid);


--
-- TOC entry 3052 (class 1259 OID 16708)
-- Name: IND-file_order-fo_category_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_order-fo_category_id" ON public.file_order USING btree (fo_category_id);


--
-- TOC entry 3053 (class 1259 OID 16709)
-- Name: IND-file_order-fo_or_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_order-fo_or_id" ON public.file_order USING btree (fo_or_id);


--
-- TOC entry 3054 (class 1259 OID 16710)
-- Name: IND-file_order-fo_pq_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_order-fo_pq_id" ON public.file_order USING btree (fo_pq_id);


--
-- TOC entry 3057 (class 1259 OID 16711)
-- Name: IND-file_product_quote-fpq_pq_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_product_quote-fpq_pq_id" ON public.file_product_quote USING btree (fpq_pq_id);


--
-- TOC entry 3060 (class 1259 OID 16712)
-- Name: IND-file_share-fsh_code; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "IND-file_share-fsh_code" ON public.file_share USING btree (fsh_code);


--
-- TOC entry 3061 (class 1259 OID 16713)
-- Name: IND-file_share-fsh_expired_dt; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_share-fsh_expired_dt" ON public.file_share USING btree (fsh_expired_dt);


--
-- TOC entry 3064 (class 1259 OID 16714)
-- Name: IND-file_storage-fs_created_dt; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_storage-fs_created_dt" ON public.file_storage USING btree (fs_created_dt);


--
-- TOC entry 3065 (class 1259 OID 16715)
-- Name: IND-file_storage-fs_expired_dt; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "IND-file_storage-fs_expired_dt" ON public.file_storage USING btree (fs_expired_dt);


--
-- TOC entry 3066 (class 1259 OID 16716)
-- Name: IND-file_storage-fs_uid; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "IND-file_storage-fs_uid" ON public.file_storage USING btree (fs_uid);


--
-- TOC entry 3039 (class 1259 OID 16727)
-- Name: ccm_client_id_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ccm_client_id_idx ON ONLY public.client_chat_message USING btree (ccm_client_id);


--
-- TOC entry 3076 (class 2606 OID 16774)
-- Name: client_chat_canned_response FK-cr_category_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.client_chat_canned_response
    ADD CONSTRAINT "FK-cr_category_id" FOREIGN KEY (cr_category_id) REFERENCES public.client_chat_canned_response_category(crc_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 3077 (class 2606 OID 16779)
-- Name: file_case FK-file_case-fc_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_case
    ADD CONSTRAINT "FK-file_case-fc_fs_id" FOREIGN KEY (fc_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3078 (class 2606 OID 16784)
-- Name: file_client FK-file_client-fcl_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_client
    ADD CONSTRAINT "FK-file_client-fcl_fs_id" FOREIGN KEY (fcl_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3082 (class 2606 OID 16789)
-- Name: file_order FK-file_client-fo_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_order
    ADD CONSTRAINT "FK-file_client-fo_fs_id" FOREIGN KEY (fo_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3079 (class 2606 OID 16794)
-- Name: file_lead FK-file_lead-fld_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_lead
    ADD CONSTRAINT "FK-file_lead-fld_fs_id" FOREIGN KEY (fld_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3080 (class 2606 OID 16799)
-- Name: file_log FK-file_log-fl_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_log
    ADD CONSTRAINT "FK-file_log-fl_fs_id" FOREIGN KEY (fl_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3081 (class 2606 OID 16804)
-- Name: file_log FK-file_log-fl_fsh_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_log
    ADD CONSTRAINT "FK-file_log-fl_fsh_id" FOREIGN KEY (fl_fsh_id) REFERENCES public.file_share(fsh_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 3083 (class 2606 OID 16809)
-- Name: file_product_quote FK-file_product_quote-fpq_fs_id-fpq_pq_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_product_quote
    ADD CONSTRAINT "FK-file_product_quote-fpq_fs_id-fpq_pq_id" FOREIGN KEY (fpq_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3084 (class 2606 OID 16814)
-- Name: file_share FK-file_share-fsh_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_share
    ADD CONSTRAINT "FK-file_share-fsh_fs_id" FOREIGN KEY (fsh_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3085 (class 2606 OID 16819)
-- Name: file_user FK-file_user-fus_fs_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.file_user
    ADD CONSTRAINT "FK-file_user-fus_fs_id" FOREIGN KEY (fus_fs_id) REFERENCES public.file_storage(fs_id) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2021-09-20 13:31:07 EEST

--
-- PostgreSQL database dump complete
--

